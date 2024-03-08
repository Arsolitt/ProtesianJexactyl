import tw from 'twin.macro';
import useFlash from '@/plugins/useFlash';
import Can from '@/components/elements/Can';
import { boolean, object, string } from 'yup';
import { ServerContext } from '@/state/server';
import Field from '@/components/elements/Field';
import React, { useEffect, useState } from 'react';
import { Textarea } from '@/components/elements/Input';
import getServerBackups from '@/api/swr/getServerBackups';
import { Button } from '@/components/elements/button/index';
import FormikSwitch from '@/components/elements/FormikSwitch';
import FlashMessageRender from '@/components/FlashMessageRender';
import Modal, { RequiredModalProps } from '@/components/elements/Modal';
import createServerBackup from '@/api/server/backups/createServerBackup';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import { Field as FormikField, Form, Formik, FormikHelpers, useFormikContext } from 'formik';

interface Values {
    name: string;
    ignored: string;
    isLocked: boolean;
}

const ModalContent = ({ ...props }: RequiredModalProps) => {
    const { isSubmitting } = useFormikContext<Values>();

    return (
        <Modal {...props} showSpinnerOverlay={isSubmitting}>
            <Form>
                <FlashMessageRender byKey={'backups:create'} css={tw`mb-4`} />
                <h2 css={tw`text-2xl mb-6`}>Создание бэкапа сервера</h2>
                <Field
                    name={'name'}
                    label={'Название'}
                    description={'Необязательно. Используется, чтобы тебе было проще их различать.'}
                />
                <div css={tw`mt-6`}>
                    <FormikFieldWrapper
                        name={'ignored'}
                        label={'Игнорируемые файлы и папки'}
                        description={`
                            Впиши сюда файлы или папки, которые следует игнорировать при создании этой резервной копии.
                            Оставь пустым, чтобы сохранить всё. Если тут пусто и в корневой папке есть файл .pteroignore, то будет использоваться он.
                            Поддерживается обобщение через звёздочку * или отмена правил префиксом !
                        `}
                    >
                        <FormikField as={Textarea} name={'ignored'} rows={6} />
                    </FormikFieldWrapper>
                </div>
                <Can action={'backup.delete'}>
                    <div css={tw`mt-6 bg-neutral-700 border border-neutral-800 shadow-inner p-4 rounded`}>
                        <FormikSwitch
                            name={'isLocked'}
                            label={'Закрыть'}
                            description={'Повесить замочек, чтобы предотвратить удаление, пока ты его не откроешь.'}
                        />
                    </div>
                </Can>
                <div css={tw`flex justify-end mt-6`}>
                    <Button.Success type={'submit'} disabled={isSubmitting}>
                        Начать создание
                    </Button.Success>
                </div>
            </Form>
        </Modal>
    );
};

export default () => {
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const [visible, setVisible] = useState(false);
    const { mutate } = getServerBackups();

    useEffect(() => {
        clearFlashes('backups:create');
    }, [visible]);

    const submit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('backups:create');
        createServerBackup(uuid, values)
            .then((backup) => {
                mutate(
                    (data) => ({ ...data, items: data.items.concat(backup), backupCount: data.backupCount + 1 }),
                    false
                );
                setVisible(false);
            })
            .catch((error) => {
                clearAndAddHttpError({ key: 'backups:create', error });
                setSubmitting(false);
            });
    };

    return (
        <>
            {visible && (
                <Formik
                    onSubmit={submit}
                    initialValues={{ name: '', ignored: '', isLocked: false }}
                    validationSchema={object().shape({
                        name: string().max(191),
                        ignored: string(),
                        isLocked: boolean(),
                    })}
                >
                    <ModalContent appear visible={visible} onDismissed={() => setVisible(false)} />
                </Formik>
            )}
            <Button.Success css={tw`w-full sm: w-auto`} onClick={() => setVisible(true)}>
                Создать
            </Button.Success>
        </>
    );
};
