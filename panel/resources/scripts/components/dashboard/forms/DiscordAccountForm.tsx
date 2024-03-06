import React from 'react';
import { useStoreState } from '@/state/hooks';
import { Button } from '@/components/elements/button';
import { linkDiscord, unlinkDiscord } from '@/api/account/discord';

export default () => {
    const discordId = useStoreState((state) => state.user.data!.discordId);

    const link = () => {
        linkDiscord().then((data) => {
            window.location.href = data;
        });
    };

    const unlink = () => {
        unlinkDiscord().then(() => {
            window.location.href = '/account';
        });
    };

    return (
        <>
            {discordId ? (
                <>
                    <p className={'text-gray-400'}>Твой аккаунт связан с Discord: {discordId}</p>
                    <Button.Danger className={'mt-4'} onClick={() => unlink()}>
                        Отвязать Discord
                    </Button.Danger>
                </>
            ) : (
                <>
                    <p className={'text-gray-400'}>Твой аккаунт не связан с Discord.</p>
                    <Button.Success className={'mt-4'} onClick={() => link()}>
                        Привязать Discord
                    </Button.Success>
                </>
            )}
        </>
    );
};
