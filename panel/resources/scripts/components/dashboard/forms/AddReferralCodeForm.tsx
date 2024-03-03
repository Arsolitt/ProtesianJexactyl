import React from 'react';
import * as Yup from 'yup';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import { useStoreState } from '@/state/hooks';
import Field from '@/components/elements/Field';
import { Form, Formik, FormikHelpers } from 'formik';
import { Actions, useStoreActions } from 'easy-peasy';
import { Button } from '@/components/elements/button/index';
import useReferralCode from '@/api/account/useReferralCode';

interface Values {
    code: string;
    password: string;
}

const schema = Yup.object().shape({
    code: Yup.string().length(16).required(),
    password: Yup.string().required('You must provide your current account password.'),
});

export default () => {
    const code = useStoreState((state) => state.user.data!.referralCode);
    const { clearFlashes, addFlash } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const submit = (values: Values, { resetForm, setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('account:referral');

        useReferralCode({ ...values })
            .then(() =>
                addFlash({
                    type: 'success',
                    key: 'account:referral',
                    message: 'Код пригласившего добавлен',
                })
            )
            .catch((error) =>
                addFlash({
                    type: 'danger',
                    key: 'account:referral',
                    title: 'Error',
                    message: httpErrorToHuman(error),
                })
            )
            .then(() => {
                resetForm();
                setSubmitting(false);

                // @ts-expect-error this is valid
                window.location = '/account';
            });
    };

    return (
        <>
            {code ? (
                <p className={'my-2 text-gray-400'}>
                    У тебя уже добавлен код пригласившего
                    <span className={'bg-gray-800 rounded p-1 ml-2'}>{code}</span>
                </p>
            ) : (
                <Formik onSubmit={submit} initialValues={{ code: '', password: '' }} validationSchema={schema}>
                    {({ isSubmitting, isValid }) => (
                        <React.Fragment>
                            <Form className={'m-0'}>
                                <Field id={'code'} type={'text'} name={'code'} label={'Введи код'} />
                                <div className={'mt-6'}>
                                    <Field
                                        id={'confirm_password'}
                                        type={'password'}
                                        name={'password'}
                                        label={'Пароль'}
                                    />
                                </div>
                                <div className={'mt-6'}>
                                    <Button.Success disabled={isSubmitting || !isValid}>
                                        Использовать код
                                    </Button.Success>
                                </div>
                            </Form>
                        </React.Fragment>
                    )}
                </Formik>
            )}
        </>
    );
};
