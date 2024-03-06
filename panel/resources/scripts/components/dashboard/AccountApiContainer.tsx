import tw from 'twin.macro';
import { format } from 'date-fns';
import * as Icon from 'react-feather';
import Code from '@/components/elements/Code';
import { useFlashKey } from '@/plugins/useFlash';
import React, { useEffect, useState } from 'react';
import deleteApiKey from '@/api/account/deleteApiKey';
import { Dialog } from '@/components/elements/dialog';
import ContentBox from '@/components/elements/ContentBox';
import GreyRowBox from '@/components/elements/GreyRowBox';
import getApiKeys, { ApiKey } from '@/api/account/getApiKeys';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import PageContentBlock from '@/components/elements/PageContentBlock';
import CreateApiKeyForm from '@/components/dashboard/forms/CreateApiKeyForm';

export default () => {
    const [loading, setLoading] = useState(true);
    const [keys, setKeys] = useState<ApiKey[]>([]);
    const [deleteIdentifier, setDeleteIdentifier] = useState('');
    const { clearAndAddHttpError } = useFlashKey('account');

    useEffect(() => {
        getApiKeys()
            .then((keys) => setKeys(keys))
            .then(() => setLoading(false))
            .catch((error) => clearAndAddHttpError(error));
    }, []);

    const doDeletion = (identifier: string) => {
        setLoading(true);

        clearAndAddHttpError();
        deleteApiKey(identifier)
            .then(() => setKeys((s) => [...(s || []).filter((key) => key.identifier !== identifier)]))
            .catch((error) => clearAndAddHttpError(error))
            .then(() => {
                setLoading(false);
                setDeleteIdentifier('');
            });
    };

    return (
        <PageContentBlock
            title={'Account API'}
            description={'Create API keys to interact with the Panel.'}
            showFlashKey={'account'}
        >
            <div className={'md:flex flex-nowrap my-10'}>
                <ContentBox title={'Create API Key'} css={tw`flex-none w-full md:w-1/2`}>
                    <CreateApiKeyForm onKeyCreated={(key) => setKeys((s) => [...s!, key])} />
                </ContentBox>
                <ContentBox title={'API Keys'} css={tw`flex-1 overflow-hidden mt-8 md:mt-0 md:ml-8`}>
                    <SpinnerOverlay visible={loading} />
                    <Dialog.Confirm
                        title={'Delete API Key'}
                        confirm={'Delete Key'}
                        open={!!deleteIdentifier}
                        onClose={() => setDeleteIdentifier('')}
                        onConfirmed={() => doDeletion(deleteIdentifier)}
                    >
                        All requests using the <Code>{deleteIdentifier}</Code> key will be invalidated.
                    </Dialog.Confirm>
                    {keys.length === 0 ? (
                        <p css={tw`text-center text-sm`}>
                            {loading ? 'Loading...' : 'No API keys exist for this account.'}
                        </p>
                    ) : (
                        keys.map((key, index) => (
                            <GreyRowBox
                                key={key.identifier}
                                css={[tw`bg-neutral-700 flex items-center`, index > 0 && tw`mt-2`]}
                            >
                                <Icon.Key css={tw`text-neutral-300`} />
                                <div css={tw`ml-4 flex-1 overflow-hidden`}>
                                    <p css={tw`text-sm break-words`}>{key.description}</p>
                                    <p css={tw`text-2xs text-neutral-300 uppercase`}>
                                        Last used:&nbsp;
                                        {key.lastUsedAt ? format(key.lastUsedAt, 'MMM do, yyyy HH:mm') : 'Never'}
                                    </p>
                                </div>
                                <p css={tw`text-sm ml-4 hidden md:block`}>
                                    <code css={tw`font-mono py-1 px-2 bg-neutral-900 rounded`}>{key.identifier}</code>
                                </p>
                                <button css={tw`ml-4 p-2 text-sm`} onClick={() => setDeleteIdentifier(key.identifier)}>
                                    <Icon.Trash
                                        css={tw`text-neutral-400 hover:text-red-400 transition-colors duration-150`}
                                    />
                                </button>
                            </GreyRowBox>
                        ))
                    )}
                </ContentBox>
            </div>
        </PageContentBlock>
    );
};
