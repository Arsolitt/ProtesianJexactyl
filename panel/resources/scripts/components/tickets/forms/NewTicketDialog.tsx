import React from 'react';
import tw from 'twin.macro';
import { object, string } from 'yup';
import styled from 'styled-components';
import useFlash from '@/plugins/useFlash';
import { httpErrorToHuman } from '@/api/http';
import { createTicket } from '@/api/account/tickets';
import { Field, Form, Formik, FormikHelpers } from 'formik';
import { Button } from '@/components/elements/button/index';
import Input, { Textarea } from '@/components/elements/Input';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import { Dialog, DialogProps } from '@/components/elements/dialog';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';

interface Values {
    title: string;
    description: string;
}

const CustomTextarea = styled(Textarea)`
    ${tw`h-32`}
`;

export default ({ open, onClose }: DialogProps) => {
    const { addError, clearFlashes } = useFlash();

    const submit = (values: Values, { setSubmitting, resetForm }: FormikHelpers<Values>) => {
        clearFlashes('tickets');

        createTicket(values.title, values.description)
            .then((data) => {
                resetForm();
                setSubmitting(false);

                // @ts-expect-error this is valid
                window.location = `/tickets/${data.id}`;
            })
            .catch((error) => {
                setSubmitting(false);

                addError({ key: 'tickets', message: httpErrorToHuman(error) });
            });
    };

    return (
        <Dialog open={open} onClose={onClose} title={'Создать новое обращение'} description={''}>
            <Formik
                onSubmit={submit}
                initialValues={{ title: '', description: '' }}
                validationSchema={object().shape({
                    allowedIps: string(),
                    description: string().required().min(4),
                })}
            >
                {({ isSubmitting }) => (
                    <Form className={'mt-6'}>
                        <SpinnerOverlay visible={isSubmitting} />
                        <FormikFieldWrapper
                            label={'Тема'}
                            name={'title'}
                            description={'Кратко укажи суть обращения'}
                            className={'mb-6'}
                        >
                            <Field name={'title'} as={Input} />
                        </FormikFieldWrapper>
                        <FormikFieldWrapper
                            label={'Описание'}
                            name={'description'}
                            description={
                                'А вот тут уже нужно как можно больше подробностей. Картинки, видео, ссылки. Всё, что может помочь в решении проблемы.'
                            }
                        >
                            <Field name={'description'} as={CustomTextarea} />
                        </FormikFieldWrapper>
                        <div className={'flex justify-end mt-6'}>
                            <Button.Success type={'submit'}>Создать</Button.Success>
                        </div>
                    </Form>
                )}
            </Formik>
        </Dialog>
    );
};
