import tw from 'twin.macro';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import { ServerContext } from '@/state/server';
import React, { useEffect, useState } from 'react';
import { Actions, useStoreActions } from 'easy-peasy';
import { Dialog } from '@/components/elements/dialog';
import reinstallServer from '@/api/server/reinstallServer';
import { Button } from '@/components/elements/button/index';
import TitledGreyBox from '@/components/elements/TitledGreyBox';

export default () => {
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const [modalVisible, setModalVisible] = useState(false);
    const { addFlash, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const reinstall = () => {
        clearFlashes('settings');
        reinstallServer(uuid)
            .then(() => {
                addFlash({
                    key: 'settings',
                    type: 'success',
                    message: 'На твоём сервере начался процесс переустановки. Приходи через пару минут.',
                });
            })
            .catch((error) => {
                console.error(error);

                addFlash({ key: 'settings', type: 'danger', message: httpErrorToHuman(error) });
            })
            .then(() => setModalVisible(false));
    };

    useEffect(() => {
        clearFlashes();
    }, []);

    return (
        <TitledGreyBox title={'Переустановить сервер'} css={tw`relative`}>
            <Dialog.Confirm
                open={modalVisible}
                title={'Подтверждение переустановки'}
                confirm={'Да, переустановить'}
                onClose={() => setModalVisible(false)}
                onConfirmed={reinstall}
            >
                Твой сервер будет оставновлен и некоторые файлы могут быть удалены/изменены. Ты уверен, что хочешь
                продолжить?
            </Dialog.Confirm>
            <p css={tw`text-sm`}>
                Переустановка сервера остановит его и запустит скрипт начальной установки.&nbsp;
                <strong css={tw`font-medium`}>
                    Некоторые файлы могут быть удалены/изменены. Если есть что-то важное - сделай бэкап.
                </strong>
            </p>
            <div css={tw`mt-6 text-right`}>
                <Button.Danger variant={Button.Variants.Secondary} onClick={() => setModalVisible(true)}>
                    Переустановить сервер
                </Button.Danger>
            </div>
        </TitledGreyBox>
    );
};
