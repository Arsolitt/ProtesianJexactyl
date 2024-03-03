import useSWR from 'swr';
import { object, string } from 'yup';
import * as Icon from 'react-feather';
import useFlash from '@/plugins/useFlash';
import { ServerContext } from '@/state/server';
import React, { useEffect, useState } from 'react';
import { Dialog } from '@/components/elements/dialog';
import { Button } from '@/components/elements/button';
import { Field, Form, Formik, FormikHelpers } from 'formik';
import installPlugin from '@/api/server/plugins/installPlugin';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import getPlugins, { Plugin } from '@/api/server/plugins/getPlugins';
import ServerContentBlock from '@/components/elements/ServerContentBlock';

interface Values {
    query: string;
}

export default () => {
    const [query, setQuery] = useState('');
    const [open, setOpen] = useState(false);
    const { clearFlashes, addFlash, clearAndAddHttpError } = useFlash();
    const [pluginId, setPluginId] = useState<number>(0);
    const [pluginName, setPluginName] = useState<string>('');
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);

    const { data, error } = useSWR<Plugin>([uuid, query, '/plugins'], (uuid, query) => getPlugins(uuid, query));

    useEffect(() => {
        if (!error) {
            clearFlashes('server:plugins');
        } else {
            clearAndAddHttpError({ key: 'server:plugins', error });
        }
    }, [error]);

    const submit = ({ query }: Values, { setSubmitting }: FormikHelpers<Values>) => {
        setQuery(query);
        setSubmitting(false);
    };

    const doDownload = (id: number, name: string) => {
        console.log('Installing plugin with ID ' + id);
        installPlugin(uuid, id, name)
            .then(() => setOpen(false))
            .then(() =>
                addFlash({
                    key: 'server:plugins',
                    type: 'success',
                    message: 'Плагин успешно установлен',
                })
            )
            .catch((error) => clearAndAddHttpError(error));
    };

    return (
        <ServerContentBlock
            title={'Плагины'}
            description={'Устанавливай плагины для Spigot/Spigot-based ядер!'}
            showFlashKey={'server:plugins'}
        >
            <Formik
                onSubmit={submit}
                initialValues={{ query: '' }}
                validationSchema={object().shape({
                    query: string().required(),
                })}
            >
                <Form className={'j-up'}>
                    <div className={'grid grid-cols-12 mb-10'}>
                        <div className={'col-span-11 mr-4'}>
                            <Field
                                name={'query'}
                                placeholder={'Введи название для поиска...'}
                                className={'p-3 text-sm w-full bg-gray-800 rounded'}
                            />
                        </div>
                        <Button.Success type={'submit'}>
                            Поиск <Icon.Search size={18} className={'ml-1'} />
                        </Button.Success>
                    </div>
                </Form>
            </Formik>
            <Dialog.Confirm
                open={open}
                onClose={() => setOpen(false)}
                title={'Установка плагина'}
                confirm={'Установить'}
                onConfirmed={() => doDownload(pluginId, pluginName)}
            >
                Ты уверен, что хочешь установить этот плагин?
            </Dialog.Confirm>
            {!data ? null : (
                <>
                    {!data.plugins ? (
                        <p className={'text-gray-400 text-center'}>Жду, пока ты введёшь поисковый запрос...</p>
                    ) : (
                        <>
                            {data.plugins.length < 1 ? (
                                <p>Не могу найти плагины по твоему запросу :(</p>
                            ) : (
                                <div className={'lg:grid lg:grid-cols-3 p-2'}>
                                    {data.plugins.map((plugin, key) => (
                                        <>
                                            <TitledGreyBox title={plugin.name} key={key} className={'m-2'}>
                                                <div className={'lg:grid lg:grid-cols-5'}>
                                                    <div className={'lg:col-span-4'}>
                                                        <p className={'text-sm line-clamp-1'}>{plugin.tag}</p>
                                                        <p className={'text-xs text-gray-400'}>
                                                            {`https://api.spiget.org/v2/resources/${plugin.id}/go`}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        {plugin.premium ? (
                                                            <Button.Text className={'m-1'} disabled>
                                                                <Icon.DownloadCloud size={18} />
                                                            </Button.Text>
                                                        ) : (
                                                            <Button.Success
                                                                className={'m-1'}
                                                                onClick={() => {
                                                                    setPluginId(plugin.id);
                                                                    setPluginName(plugin.name);
                                                                    setOpen(true);
                                                                }}
                                                            >
                                                                <Icon.DownloadCloud size={18} />
                                                            </Button.Success>
                                                        )}
                                                        <a
                                                            target={'_blank'}
                                                            href={`https://api.spiget.org/v2/resources/${plugin.id}/go`}
                                                            rel='noreferrer'
                                                        >
                                                            <Button.Text className={'m-1'}>
                                                                <Icon.ExternalLink size={18} />
                                                            </Button.Text>
                                                        </a>
                                                    </div>
                                                </div>
                                            </TitledGreyBox>
                                        </>
                                    ))}
                                </div>
                            )}
                        </>
                    )}
                </>
            )}
        </ServerContentBlock>
    );
};
