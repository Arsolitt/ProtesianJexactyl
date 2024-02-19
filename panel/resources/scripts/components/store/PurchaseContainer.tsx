import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import styled from 'styled-components/macro';
import { useStoreState } from '@/state/hooks';
import React, { useEffect, useState } from 'react';
import Spinner from '@/components/elements/Spinner';
import ContentBox from '@/components/elements/ContentBox';
import { getResources, Resources } from '@/api/store/getResources';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PurchaseForm from '@/components/store/forms/PurchaseForm';

const Container = styled.div`
    ${tw`flex flex-wrap`};

    & > div {
        ${tw`w-full`};

        ${breakpoint('sm')`
      width: calc(50% - 1rem);
    `}

        ${breakpoint('md')`
      ${tw`w-auto flex-1`};
    `}
    }
`;

export default () => {
    const [resources, setResources] = useState<Resources>();
    const gateways = useStoreState((state) => state.storefront.data!.gateways);

    useEffect(() => {
        getResources().then((resources) => setResources(resources));
    }, []);

    if (!resources) return <Spinner size={'large'} centered />;

    return (
        <PageContentBlock title={'Account Balance'} description={'Purchase credits easily via Stripe or PayPal.'}>
            <Container className={'lg:grid lg:grid-cols-2 my-10'}>
                <ContentBox title={'Account Balance'} showFlashes={'account:balance'} css={tw`sm: mt-0`}>
                    <h1 css={tw`text-7xl flex justify-center items-center`}>
                        {resources.balance} <span className={'text-base ml-4'}>credits</span>
                    </h1>
                </ContentBox>
                <ContentBox
                    title={'Purchase credits'}
                    showFlashes={'account:balance'}
                    css={tw`mt-8 sm: mt-0
                        sm: ml-8`}
                >
                    {gateways.length < 1 ? (
                        <p className={'text-gray-400 text-sm text-center'}>
                            Payment gateways are unavailable at this time.
                        </p>
                    ) : (
                        <>{<PurchaseForm />}</>
                    )}
                </ContentBox>
            </Container>
        </PageContentBlock>
    );
};
