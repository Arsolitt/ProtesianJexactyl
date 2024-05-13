import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import styled from 'styled-components/macro';
import { useStoreState } from '@/state/hooks';
import React from 'react';
import Spinner from '@/components/elements/Spinner';
import ContentBox from '@/components/elements/ContentBox';
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
    const user = useStoreState((state) => state.user.data!);
    const gateways = useStoreState((state) => state.storefront.data!.gateways);

    if (!user) return <Spinner size={'large'} centered />;

    return (
        <PageContentBlock title={'Биллинг'} description={'Пополнение баланса. Тут можно отдать свои денюжки.'}>
            <Container className={'lg:grid lg:grid-cols-2 my-10'}>
                <ContentBox title={'Текущий баланс'} showFlashes={'account:balance'} css={tw`sm: mt-0`}>
                    <h1 css={tw`text-7xl flex justify-center items-center`}>{user.credits.toFixed(2)} ₽</h1>
                </ContentBox>
                <ContentBox
                    title={'Пополнение баланса'}
                    showFlashes={'account:balance'}
                    css={tw`mt-8 sm: mt-0
                        sm: ml-8`}
                >
                    {gateways.length < 1 ? (
                        <p className={'text-gray-400 text-sm text-center'}>Нет доступных платёжных шлюзов :(</p>
                    ) : (
                        <>{<PurchaseForm gateways={gateways} />}</>
                    )}
                </ContentBox>
            </Container>
        </PageContentBlock>
    );
};
