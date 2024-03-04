import tw from 'twin.macro';
import * as Icon from 'react-feather';
import React, { useState } from 'react';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import { Subuser } from '@/state/server/subusers';
import { Actions, useStoreActions } from 'easy-peasy';
import { Dialog } from '@/components/elements/dialog';

import deleteSubuser from '@/api/server/users/deleteSubuser';

export default ({ subuser }: { subuser: Subuser }) => {
    const [showConfirmation, setShowConfirmation] = useState(false);

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const removeSubuser = ServerContext.useStoreActions((actions) => actions.subusers.removeSubuser);
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const doDeletion = () => {
        clearFlashes('users');
        deleteSubuser(uuid, subuser.uuid)
            .then(() => {
                removeSubuser(subuser.uuid);
            })
            .catch((error) => {
                console.error(error);
                addError({ key: 'users', message: httpErrorToHuman(error) });
                setShowConfirmation(false);
            });
    };

    return (
        <>
            <Dialog.Confirm
                open={showConfirmation}
                onClose={() => setShowConfirmation(false)}
                title={'Удаление пользователя'}
                confirm={'Удалить'}
                onConfirmed={doDeletion}
            >
                Ты уверен, что хочешь удалить пользователя? Он моментально потеряет доступ к серверу!
            </Dialog.Confirm>
            <button
                type={'button'}
                aria-label={'Delete subuser'}
                css={tw`block text-sm p-2 text-inert-500 hover:text-negative-500 transition-colors duration-150`}
                onClick={() => setShowConfirmation(true)}
            >
                <Icon.Trash />
            </button>
        </>
    );
};
