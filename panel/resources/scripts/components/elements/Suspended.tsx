import tw from 'twin.macro';
import React, { useState } from 'react';
import useFlash from '@/plugins/useFlash';
import Code from '@/components/elements/Code';
import { ServerContext } from '@/state/server';
import Input from '@/components/elements/Input';
import renewServer from '@/api/server/renewServer';
import deleteServer from '@/api/server/deleteServer';
import { Button } from '@/components/elements/button';
import { Dialog } from '@/components/elements/dialog';
import ServerErrorSvg from '@/assets/images/server_error.svg';
import FlashMessageRender from '@/components/FlashMessageRender';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import PageContentBlock from '@/components/elements/PageContentBlock';
// import { useStoreState } from 'easy-peasy';

export default () => {
    const [name, setName] = useState('');

    const [isSubmit, setSubmit] = useState(false);
    const [renewDialog, setRenewDialog] = useState(false);
    const [deleteDialog, setDeleteDialog] = useState(false);
    const [confirmDialog, setConfirmDialog] = useState(false);

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    // const store = useStoreState((state) => state.storefront.data!);
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const serverName = ServerContext.useStoreState((state) => state.server.data!.name);

    const doRenewal = () => {
        clearFlashes('server:renewal');
        setSubmit(true);

        renewServer(uuid)
            .then(() => {
                setSubmit(false);
                // @ts-expect-error this is valid
                window.location = '/home';
            })
            .catch((error) => {
                clearAndAddHttpError({ key: 'server:renewal', error });
                setSubmit(false);
            });
    };

    const doDeletion = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        e.stopPropagation();

        clearFlashes('server:renewal');
        setSubmit(true);

        deleteServer(uuid, name)
            .then(() => {
                setSubmit(false);
                // @ts-expect-error this is valid
                window.location = '/store';
            })
            .catch((error) => {
                clearAndAddHttpError({ key: 'server:renewal', error });
                setSubmit(false);
            });
    };

    return (
        <>
            <Dialog.Confirm
                open={renewDialog}
                onClose={() => setRenewDialog(false)}
                title={'Подтвердить разморозку'}
                confirm={'Разморозить'}
                onConfirmed={() => doRenewal()}
            >
                <SpinnerOverlay visible={isSubmit} />
                Ты уверен, что хочешь разморозить сервер?
            </Dialog.Confirm>
            <Dialog.Confirm
                open={deleteDialog}
                onClose={() => setDeleteDialog(false)}
                title={'Подтвердить удаление'}
                confirm={'Удалить'}
                onConfirmed={() => setConfirmDialog(true)}
            >
                <SpinnerOverlay visible={isSubmit} />
                Это действие удалит твой сервер из системы. Действие необратимо!
            </Dialog.Confirm>
            <form id={'delete-suspended-server-form'} onSubmit={doDeletion}>
                <Dialog open={confirmDialog} title={'Подтвердить удаление'} onClose={() => setConfirmDialog(false)}>
                    {name !== serverName && (
                        <>
                            <p className={'my-2 text-gray-400'}>
                                Введи <Code>{serverName}</Code> ниже.
                            </p>
                            <Input type={'text'} value={name} onChange={(n) => setName(n.target.value)} />
                        </>
                    )}
                    <Button.Danger
                        disabled={name !== serverName}
                        type={'submit'}
                        className={'mt-2'}
                        form={'delete-suspended-server-form'}
                    >
                        Подтвердить
                    </Button.Danger>
                </Dialog>
            </form>
            <PageContentBlock title={'Сервер заморожен!'}>
                <FlashMessageRender byKey={'server:renewal'} css={tw`mb-1`} />
                <div css={tw`flex justify-center`}>
                    <div
                        css={tw`w-full sm:w-3/4 md:w-1/2 p-12 md:p-20 bg-neutral-900 rounded-lg shadow-lg text-center relative`}
                    >
                        <img src={ServerErrorSvg} css={tw`w-2/3 h-auto select-none mx-auto`} />
                        <h2 css={tw`mt-10 font-bold text-4xl`}>Заморожен</h2>
                        {
                            <>
                                <p css={tw`text-sm my-2`}>
                                    Твой сервер заморожен за неуплату! Ты можешь его разморозить или удалить. Для
                                    разморозки потребуется иметь на балансе сумму для оплаты на сутки. Но оплата будет
                                    снята за час.
                                </p>
                                <Button.Success
                                    className={'mx-2 my-1'}
                                    onClick={() => setRenewDialog(true)}
                                    disabled={isSubmit}
                                >
                                    Разморозить
                                </Button.Success>
                                <Button.Danger
                                    className={'mx-2 my-1'}
                                    onClick={() => setDeleteDialog(true)}
                                    disabled={isSubmit}
                                >
                                    Удалить
                                </Button.Danger>
                            </>
                        }
                    </div>
                </div>
            </PageContentBlock>
        </>
    );
};
