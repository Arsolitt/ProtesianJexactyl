import tw from 'twin.macro';
import { Field, Form, Formik } from 'formik';
import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import stripe from '@/api/store/gateways/stripe';
import { Dialog } from '@/components/elements/dialog';
import { Button } from '@/components/elements/button/index';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import FlashMessageRender from '@/components/FlashMessageRender';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';

export default () => {
    const { clearAndAddHttpError } = useFlash();
    const [amount, setAmount] = useState(0);
    const [submitting, setSubmitting] = useState(false);

    const submit = () => {
        setSubmitting(true);

        stripe(amount)
            .then((url) => {
                setSubmitting(false);

                // @ts-expect-error this is valid
                window.location.href = url;
            })
            .catch((error) => {
                setSubmitting(false);

                clearAndAddHttpError({ key: 'store:stripe', error });
            });
    };

    return (
        <TitledGreyBox title={'Сумма пополнения'}>
            <Dialog open={submitting} hideCloseIcon onClose={() => undefined}>
                Сейчас будет переход на страницу оплаты!
            </Dialog>
            <FlashMessageRender byKey={'store:stripe'} css={tw`mb-2`} />
            <Formik
                onSubmit={submit}
                initialValues={{
                    amount: 100,
                }}
            >
                <Form>
                    <SpinnerOverlay size={'large'} visible={submitting} />
                    <Field name={'amount'}></Field>
                    <div css={tw`mt-6`}>
                        <Button type={'submit'} disabled={submitting}>
                            Пополнить баланс!
                        </Button>
                    </div>
                </Form>
            </Formik>
        </TitledGreyBox>
    );
};
