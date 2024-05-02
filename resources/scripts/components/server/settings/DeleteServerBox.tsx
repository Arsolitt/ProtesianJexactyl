import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import Code from '@/components/elements/Code';
import { ServerContext } from '@/state/server';
import Input from '@/components/elements/Input';
import deleteServer from '@/api/server/deleteServer';
import { Dialog } from '@/components/elements/dialog';
import { Button } from '@/components/elements/button/index';
import TitledGreyBox from '@/components/elements/TitledGreyBox';

export default () => {
    const [name, setName] = useState('');
    const [warn, setWarn] = useState(false);
    const [confirm, setConfirm] = useState(false);

    const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const serverName = ServerContext.useStoreState((state) => state.server.data!.name);

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        e.stopPropagation();
        clearFlashes('settings');

        deleteServer(uuid, name)
            .then(() => {
                setConfirm(false);
                addFlash({
                    key: 'settings',
                    type: 'success',
                    message: 'Твой сервер удалён',
                });
                // @ts-expect-error this is valid
                window.location = '/';
            })
            .catch((error) => clearAndAddHttpError({ key: 'settings', error }));
    };

    return (
        <TitledGreyBox title={'Удалить сервер'} className={'relative mb-12'}>
            <Dialog.Confirm
                open={warn}
                title={'Подтверждение удаления сервера'}
                confirm={'Да, удалить сервер'}
                onClose={() => setWarn(false)}
                onConfirmed={() => {
                    setConfirm(true);
                    setWarn(false);
                }}
            >
                Твой сервер будет удалён. Все файлы будут уничтожены без возможности восстановления. Ты уверен, что
                хочешь сделать это?
            </Dialog.Confirm>
            <form id={'delete-server-form'} onSubmit={submit}>
                <Dialog
                    open={confirm}
                    title={'Подтверждение удаления сервера'}
                    onClose={() => {
                        setConfirm(false);
                        setName('');
                    }}
                >
                    {name !== serverName && (
                        <>
                            <p className={'my-2 text-gray-400'}>
                                Введи <Code>{serverName}</Code> ниже.
                            </p>
                            <Input type={'text'} value={name} onChange={(n) => setName(n.target.value)} />
                        </>
                    )}
                    <Button.Success
                        disabled={name !== serverName}
                        type={'submit'}
                        className={'mt-2'}
                        form={'delete-server-form'}
                    >
                        Да, удалить сервер
                    </Button.Success>
                </Dialog>
            </form>
            <p className={'text-sm'}>
                Удаление сервера остановит все процессы. Удалит все файлы на сервере, и уничтожит все связанный с
                сервером ресурсы: бэкапы, базы данных и все остальные настройки.
                <br />
                <strong className={'font-bold'}>
                    Все данные будут потеряны навсегда, если ты удалишь свой сервер.
                </strong>
            </p>
            <div className={'mt-6 font-medium text-right'}>
                <Button.Danger variant={Button.Variants.Secondary} onClick={() => setWarn(true)}>
                    Удалить сервер
                </Button.Danger>
            </div>
        </TitledGreyBox>
    );
};
