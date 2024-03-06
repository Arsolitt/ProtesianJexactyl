import React from 'react';
import * as Yup from 'yup';
import tw from 'twin.macro';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import Field from '@/components/elements/Field';
import { Form, Formik, FormikHelpers } from 'formik';
import { Actions, useStoreActions } from 'easy-peasy';
import { Button } from '@/components/elements/button/index';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import updateAccountUsername from '@/api/account/updateAccountUsername';

interface Values {
    username: string;
    password: string;
}

const schema = Yup.object().shape({
    username: Yup.string().min(3, 'Никнейм не может быть короче 3 символов!').required('Нужно указать новый никнейм!'),
    password: Yup.string().required('Нужно указать текущий пароль!'),
});

export default () => {
    const { clearFlashes, addFlash } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const submit = (values: Values, { resetForm, setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('account:email');

        updateAccountUsername({ ...values })
            .then(() =>
                addFlash({
                    type: 'success',
                    key: 'account:username',
                    message: 'Твой никнейм успешно изменён',
                })
            )
            .catch((error) =>
                addFlash({
                    type: 'danger',
                    key: 'account:username',
                    title: 'Ошибка',
                    message: httpErrorToHuman(error),
                })
            )
            .then(() => {
                resetForm();
                setSubmitting(false);
            });
    };

    return (
        <Formik onSubmit={submit} initialValues={{ username: '', password: '' }} validationSchema={schema}>
            {({ isSubmitting, isValid }) => (
                <React.Fragment>
                    <SpinnerOverlay size={'large'} visible={isSubmitting} />
                    <Form css={tw`m-0`}>
                        <Field id={'new_username'} type={'username'} name={'username'} label={'Новый никнейм'} />
                        <div css={tw`mt-6`}>
                            <Field id={'confirm_password'} type={'password'} name={'password'} label={'Пароль'} />
                        </div>
                        <div css={tw`mt-6`}>
                            <Button.Success disabled={isSubmitting || !isValid}>Обновить никнейм</Button.Success>
                        </div>
                    </Form>
                </React.Fragment>
            )}
        </Formik>
    );
};
