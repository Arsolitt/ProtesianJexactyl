import tw from 'twin.macro';
import { Form, Formik } from 'formik';
import Field from '@/components/elements/Field';
import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import { Dialog } from '@/components/elements/dialog';
import { Button } from '@/components/elements/button/index';
import FlashMessageRender from '@/components/FlashMessageRender';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import { Gateway } from '@/state/storefront';
import { number, object } from 'yup';
import purchase from '@/api/store/purchase';

interface Props {
    gateways: Gateway[];
}

export interface PurchaseValues {
    gateway: string;
    amount: number;
}

export default (props: Props) => {
    const [submitting, setSubmitting] = useState(false);
    const { clearFlashes, clearAndAddHttpError } = useFlash();

    // const submit = () => {
    //     setSubmitting(true);
    //
    //     stripe(amount)
    //         .then((url) => {
    //             setSubmitting(false);
    //
    //             // @ts-expect-error this is valid
    //             window.location.href = url;
    //         })
    //         .catch((error) => {
    //             setSubmitting(false);
    //
    //             clearAndAddHttpError({ key: 'store:stripe', error });
    //         });
    // };

    const submit = (values: PurchaseValues) => {
        setSubmitting(true);
        clearFlashes('store:purchase');

        purchase(values)
            .then((data) => {
                if (!data.url) return;

                setSubmitting(false);
                clearFlashes('store:purchase');
                window.location.href = data.url;
            })
            .catch((error) => {
                setSubmitting(false);
                clearAndAddHttpError({ key: 'store:purchase', error });
            });
    };

    return (
        <Formik
            onSubmit={submit}
            initialValues={{
                amount: 50,
                gateway: props.gateways[0].id,
            }}
            validationSchema={object().shape({
                amount: number()
                    .required()
                    .integer()
                    .test('min', '', function (value) {
                        const { gateway } = this.parent;
                        const minValue = props.gateways.filter((gw) => gw.id === gateway)[0].min;
                        return (
                            value >= minValue ||
                            this.createError({
                                message: `Минимальная сумма пополнения: ${minValue}`,
                            })
                        );
                    })
                    .test('max', '', function (value) {
                        const { gateway } = this.parent;
                        const maxValue = props.gateways.filter((gw) => gw.id === gateway)[0].max;
                        return (
                            value <= maxValue ||
                            this.createError({
                                message: `Максимальная сумма пополнения: ${maxValue}`,
                            })
                        );
                    }),
            })}
        >
            <Form>
                <SpinnerOverlay size={'large'} visible={submitting} />
                <Dialog open={submitting} hideCloseIcon onClose={() => undefined}>
                    Сейчас будет переход на страницу оплаты!
                </Dialog>
                <FlashMessageRender byKey={'store:purchase'} css={tw`mb-2`} />
                <Field name={'amount'} label={'Сумма пополнения'} />
                {props.gateways.map((gateway) => (
                    <div key={gateway.name}>
                        <label className={'inline-flex gap-1'}>
                            <Field
                                type={'radio'}
                                name={'gateway'}
                                value={gateway.id}
                                checked={gateway.id === props.gateways[0].id}
                            />
                            {gateway.name}
                        </label>
                    </div>
                ))}
                <div css={tw`mt-6`}>
                    <Button type={'submit'} disabled={submitting}>
                        Пополнить баланс!
                    </Button>
                </div>
            </Form>
        </Formik>
    );
};
