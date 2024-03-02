import tw from 'twin.macro';
import asModal from '@/hoc/asModal';
import useFlash from '@/plugins/useFlash';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import Field from '@/components/elements/Field';
import ModalContext from '@/context/ModalContext';
import Switch from '@/components/elements/Switch';
import { Form, Formik, FormikHelpers } from 'formik';
import { Button } from '@/components/elements/button/index';
import FormikSwitch from '@/components/elements/FormikSwitch';
import React, { useContext, useEffect, useState } from 'react';
import FlashMessageRender from '@/components/FlashMessageRender';
import { Schedule } from '@/api/server/schedules/getServerSchedules';
import createOrUpdateSchedule from '@/api/server/schedules/createOrUpdateSchedule';
import ScheduleCheatsheetCards from '@/components/server/schedules/ScheduleCheatsheetCards';

interface Props {
    schedule?: Schedule;
}

interface Values {
    name: string;
    dayOfWeek: string;
    month: string;
    dayOfMonth: string;
    hour: string;
    minute: string;
    enabled: boolean;
    onlyWhenOnline: boolean;
}

const EditScheduleModal = ({ schedule }: Props) => {
    const { addError, clearFlashes } = useFlash();
    const { dismiss } = useContext(ModalContext);

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const appendSchedule = ServerContext.useStoreActions((actions) => actions.schedules.appendSchedule);
    const [showCheatsheet, setShowCheetsheet] = useState(false);

    useEffect(() => {
        return () => {
            clearFlashes('schedule:edit');
        };
    }, []);

    const submit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('schedule:edit');
        createOrUpdateSchedule(uuid, {
            id: schedule?.id,
            name: values.name,
            cron: {
                minute: values.minute,
                hour: values.hour,
                dayOfWeek: values.dayOfWeek,
                month: values.month,
                dayOfMonth: values.dayOfMonth,
            },
            onlyWhenOnline: values.onlyWhenOnline,
            isActive: values.enabled,
        })
            .then((schedule) => {
                setSubmitting(false);
                appendSchedule(schedule);
                dismiss();
            })
            .catch((error) => {
                console.error(error);

                setSubmitting(false);
                addError({ key: 'schedule:edit', message: httpErrorToHuman(error) });
            });
    };

    return (
        <Formik
            onSubmit={submit}
            initialValues={
                {
                    name: schedule?.name || '',
                    minute: schedule?.cron.minute || '*/5',
                    hour: schedule?.cron.hour || '*',
                    dayOfMonth: schedule?.cron.dayOfMonth || '*',
                    month: schedule?.cron.month || '*',
                    dayOfWeek: schedule?.cron.dayOfWeek || '*',
                    enabled: schedule?.isActive ?? true,
                    onlyWhenOnline: schedule?.onlyWhenOnline ?? true,
                } as Values
            }
        >
            {({ isSubmitting }) => (
                <Form>
                    <h3 css={tw`text-2xl mb-6`}>
                        {schedule ? 'Редактирование задачи планировщика' : 'Создание задачи планировщика'}
                    </h3>
                    <FlashMessageRender byKey={'schedule:edit'} css={tw`mb-6`} />
                    <Field
                        name={'name'}
                        label={'Название задачи'}
                        description={'Назови задачу, чтобы не запутаться в них!'}
                    />
                    <div css={tw`grid grid-cols-2 sm:grid-cols-5 gap-4 mt-6`}>
                        <Field name={'minute'} label={'Минуты'} />
                        <Field name={'hour'} label={'Часы'} />
                        <Field name={'dayOfMonth'} label={'День месяца'} />
                        <Field name={'month'} label={'Месяц'} />
                        <Field name={'dayOfWeek'} label={'День недели'} />
                    </div>
                    <p css={tw`text-neutral-400 text-xs mt-2`}>
                        Планировщик поддерживает использование синтаксиса Cronjob для определения времени начала
                        выполнения заданий. Используй поля выше, чтобы указать время начала выполнения задачи.
                    </p>
                    <div css={tw`mt-6 bg-neutral-900 border border-neutral-800 shadow-inner p-4 rounded`}>
                        <Switch
                            name={'show_cheatsheet'}
                            description={'Показывает подсказку по Cronjob с примерами'}
                            label={'Посмотреть подсказку'}
                            defaultChecked={showCheatsheet}
                            onChange={() => setShowCheetsheet((s) => !s)}
                        />
                        {showCheatsheet && (
                            <div css={tw`block md:flex w-full`}>
                                <ScheduleCheatsheetCards />
                            </div>
                        )}
                    </div>
                    <div css={tw`mt-6 bg-neutral-900 border border-neutral-800 shadow-inner p-4 rounded`}>
                        <FormikSwitch
                            name={'onlyWhenOnline'}
                            description={'Выполняет задачу, только если сервер включен'}
                            label={'Только при включенном сервере'}
                        />
                    </div>
                    <div css={tw`mt-6 bg-neutral-900 border border-neutral-800 shadow-inner p-4 rounded`}>
                        <FormikSwitch
                            name={'enabled'}
                            description={'Задача будет автоматически выполняться, если активирована'}
                            label={'Активировать'}
                        />
                    </div>
                    <div css={tw`mt-6 text-right`}>
                        <Button.Success className={'w-full sm:w-auto'} type={'submit'} disabled={isSubmitting}>
                            {schedule ? 'Сохранить изменения' : 'Создать задачу'}
                        </Button.Success>
                    </div>
                </Form>
            )}
        </Formik>
    );
};

export default asModal<Props>()(EditScheduleModal);
