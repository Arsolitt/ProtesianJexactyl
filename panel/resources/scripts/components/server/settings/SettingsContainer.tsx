import React from 'react';
import tw from 'twin.macro';
import Can from '@/components/elements/Can';
import { ServerContext } from '@/state/server';
import CopyOnClick from '@/components/elements/CopyOnClick';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import RenameServerBox from '@/components/server/settings/RenameServerBox';
import DeleteServerBox from '@/components/server/settings/DeleteServerBox';
import ReinstallServerBox from '@/components/server/settings/ReinstallServerBox';
import { useStoreState } from 'easy-peasy';
import Label from '@/components/elements/Label';
import { ip } from '@/lib/formatters';
import Input from '@/components/elements/Input';
import { Button } from '@/components/elements/button';

export default () => {
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const node = ServerContext.useStoreState((state) => state.server.data!.node);
    const id = ServerContext.useStoreState((state) => state.server.data!.id);
    const username = useStoreState((state) => state.user.data!.username);
    const sftp = ServerContext.useStoreState((state) => state.server.data!.sftpDetails);

    return (
        <ServerContentBlock
            title={'Настройки'}
            description={'Управление важными настройками твоего сервера.'}
            showFlashKey={'settings'}
        >
            <div className={'md:flex'}>
                <div className={'w-full md:flex-1 md:mr-10'}>
                    <Can action={'settings.rename'}>
                        <div className={'mb-6 md:mb-10'}>
                            <RenameServerBox />
                        </div>
                    </Can>
                    <TitledGreyBox title={'Служебная информация'} className={'mb-6 md:mb-10'}>
                        <div css={tw`flex items-center justify-between text-sm`}>
                            <p>Узел</p>
                            <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>{node}</code>
                        </div>
                        <CopyOnClick text={uuid}>
                            <div css={tw`flex items-center justify-between mt-2 text-sm`}>
                                <p>ID Сервера</p>
                                <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>{uuid}</code>
                            </div>
                        </CopyOnClick>
                    </TitledGreyBox>
                </div>
                <div className={'w-full mt-6 md:flex-1 md:mt-0'}>
                    <Can action={'file.sftp'}>
                        <TitledGreyBox title={'Данные для SFTP'} className={'mb-6 md:mb-10'}>
                            <div>
                                <Label>Адрес сервера</Label>
                                <CopyOnClick text={`sftp://${ip(sftp.ip)}:${sftp.port}`}>
                                    <Input type={'text'} value={`sftp://${ip(sftp.ip)}:${sftp.port}`} readOnly />
                                </CopyOnClick>
                            </div>
                            <div css={tw`mt-6`}>
                                <Label>Имя пользователя</Label>
                                <CopyOnClick text={`${username}.${id}`}>
                                    <Input type={'text'} value={`${username}.${id}`} readOnly />
                                </CopyOnClick>
                            </div>
                            <div css={tw`mt-6 flex items-center`}>
                                <div css={tw`flex-1`}>
                                    <div css={tw`border-l-4 border-cyan-500 p-3`}>
                                        <p css={tw`text-xs text-neutral-200`}>
                                            Пароль для SFTP совпадает с твоим паролем на сайте.
                                        </p>
                                    </div>
                                </div>
                                <div css={tw`ml-4`}>
                                    <a href={`sftp://${username}.${id}@${ip(sftp.ip)}:${sftp.port}`}>
                                        <Button.Text variant={Button.Variants.Secondary}>Запустить SFTP</Button.Text>
                                    </a>
                                </div>
                            </div>
                        </TitledGreyBox>
                    </Can>
                    <Can action={'settings.reinstall'}>
                        <div className={'mb-6 md:mb-10'}>
                            <ReinstallServerBox />
                        </div>
                    </Can>
                    <Can action={'*'}>
                        <DeleteServerBox />
                    </Can>
                </div>
            </div>
        </ServerContentBlock>
    );
};
