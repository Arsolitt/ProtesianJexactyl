import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import renewServer from '@/api/server/renewServer';
import { Dialog } from '@/components/elements/dialog';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';

export default () => {
    const [open, setOpen] = useState(false);
    const { addFlash, clearFlashes } = useFlash();
    const [loading, setLoading] = useState(false);
    // const store = useStoreState((state) => state.storefront.data!);
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);

    const doRenewal = () => {
        setLoading(true);
        clearFlashes('console:share');

        renewServer(uuid)
            .then(() => {
                setOpen(false);
                setLoading(false);

                addFlash({
                    key: 'console:share',
                    type: 'success',
                    message: 'Server has been renewed.',
                });
            })
            .catch((error) => {
                setOpen(false);
                setLoading(false);

                console.log(httpErrorToHuman(error));
                addFlash({
                    key: 'console:share',
                    type: 'danger',
                    message: 'Unable to renew your server. Are you sure you have enough credits?',
                });
            });
    };

    return (
        <>
            <Dialog.Confirm
                open={open}
                onClose={() => setOpen(false)}
                title={'Confirm server renewal'}
                onConfirmed={() => doRenewal()}
            >
                <SpinnerOverlay visible={loading} />
            </Dialog.Confirm>
            in 666 days{' '}
            <span className={'text-blue-500 text-sm cursor-pointer'} onClick={() => setOpen(true)}>
                {'('}Renew{')'}
            </span>
        </>
    );
};
