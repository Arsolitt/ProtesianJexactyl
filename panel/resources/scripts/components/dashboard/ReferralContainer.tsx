import tw from 'twin.macro';
import { format } from 'date-fns';
import { breakpoint } from '@/theme';
import * as Icon from 'react-feather';
import styled from 'styled-components/macro';
import { useStoreState } from '@/state/hooks';
import React, { useEffect, useState } from 'react';
import { Button } from '@/components/elements/button';
import { Dialog } from '@/components/elements/dialog';
import GreyRowBox from '@/components/elements/GreyRowBox';
import ContentBox from '@/components/elements/ContentBox';
import useFlash, { useFlashKey } from '@/plugins/useFlash';
import deleteReferralCode from '@/api/account/deleteReferralCode';
import createReferralCode from '@/api/account/createReferralCode';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import PageContentBlock from '@/components/elements/PageContentBlock';
import getReferralCodes, { ReferralCode } from '@/api/account/getReferralCodes';
import getReferralActivity, { ReferralActivity } from '@/api/account/getReferralActivity';
import { ru } from 'date-fns/locale';
import CopyOnClick from '@/components/elements/CopyOnClick';

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
    const { addFlash } = useFlash();
    const [code, setCode] = useState('');
    const [loading, setLoading] = useState(false);
    const [codes, setCodes] = useState<ReferralCode[]>([]);
    const [activity, setActivity] = useState<ReferralActivity[]>([]);
    const { clearFlashes, clearAndAddHttpError } = useFlashKey('referrals');
    const reward = useStoreState((state) => state.user.data?.reward);

    useEffect(() => {
        clearFlashes();
        setLoading(true);

        getReferralCodes()
            .then((codes) => {
                setCodes(codes);
                setLoading(false);
            })
            .catch((error) => clearAndAddHttpError(error));

        getReferralActivity()
            .then((activity) => {
                setActivity(activity);
                setLoading(false);
            })
            .catch((error) => clearAndAddHttpError(error));
    }, []);

    const doCreation = () => {
        clearFlashes();
        setLoading(true);

        createReferralCode()
            .then(() => {
                getReferralCodes().then((codes) => setCodes(codes));
                addFlash({
                    type: 'success',
                    key: 'referrals',
                    message: 'Код для приглашения успешно создан',
                });
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => {
                setLoading(false);
            });
    };

    const doDeletion = (code: string) => {
        clearFlashes();
        setLoading(true);

        deleteReferralCode(code)
            .then(() => {
                getReferralCodes().then((codes) => setCodes(codes));
                addFlash({
                    type: 'success',
                    key: 'referrals',
                    message: 'Код для приглашения удалён',
                });
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => {
                setLoading(false);
                setCode('');
            });
    };

    return (
        <PageContentBlock title={'Рефералы'} description={'Создай код и зови друзей!'} showFlashKey={'referrals'}>
            <Container className={'lg:grid lg:grid-cols-3 my-10'}>
                <ContentBox title={'Твои реферальные коды'} css={tw`sm:mt-0`}>
                    <Dialog.Confirm
                        title={'Удалить реферальный код'}
                        confirm={'Удалить'}
                        open={!!code}
                        onClose={() => setCode('')}
                        onConfirmed={() => doDeletion(code)}
                    >
                        Никто не сможет больше использовать этот код для регистрации.
                    </Dialog.Confirm>
                    <SpinnerOverlay visible={loading} />
                    {codes.length === 0 ? (
                        <p css={tw`text-center my-2`}>
                            {!loading && 'Ни одного реферального кода для твоего аккаунта не существует :('}
                        </p>
                    ) : (
                        codes.map((code, index) => (
                            <GreyRowBox
                                key={code.code}
                                css={[tw`bg-neutral-900 flex items-center`, index > 0 && tw`mt-2`]}
                            >
                                <Icon.GitBranch css={tw`text-neutral-300`} />
                                <div css={tw`ml-4 flex-1 overflow-hidden`}>
                                    <CopyOnClick text={code.code}>
                                        <p css={tw`text-sm break-words`}>{code.code}</p>
                                    </CopyOnClick>
                                    <p css={tw`text-2xs text-neutral-300 uppercase`}>
                                        Создан:&nbsp;
                                        {code.createdAt
                                            ? format(code.createdAt, 'MMM do, yyyy HH:mm', { locale: ru })
                                            : 'Никогда'}
                                    </p>
                                </div>
                                <button css={tw`ml-4 p-2 text-sm`} onClick={() => setCode(code.code)}>
                                    <Icon.Trash
                                        css={tw`text-neutral-400 hover:text-red-400 transition-colors duration-150`}
                                    />
                                </button>
                            </GreyRowBox>
                        ))
                    )}
                    <Button.Success onClick={() => doCreation()} className={'mt-4'}>
                        Создать
                    </Button.Success>
                </ContentBox>
                <ContentBox title={'Условия программы'} css={tw`mt-8 sm:mt-0 sm:ml-8`}>
                    <h1 css={tw`text-xl`}>
                        Ты будешь получать <span className={'text-main-500'}>{reward}</span>% от пополнения твоих
                        рефералов!
                    </h1>
                </ContentBox>
                <ContentBox title={'Приглашённые пользователи'} css={tw`mt-8 sm:mt-0 sm:ml-8`}>
                    <SpinnerOverlay visible={loading} />
                    {activity.length === 0 ? (
                        <p css={tw`text-center my-2`}>
                            {!loading && 'На твоём аккаунте нет реферальной активности :('}
                        </p>
                    ) : (
                        activity.map((act, index) => (
                            <GreyRowBox
                                key={act.code}
                                css={[tw`bg-neutral-900 flex items-center`, index > 0 && tw`mt-2`]}
                            >
                                <Icon.GitBranch css={tw`text-neutral-300`} />
                                <div css={tw`ml-4 flex-1 overflow-hidden`}>
                                    <p css={tw`text-sm break-words`}>
                                        {act.userEmail} (ID: {act.userId})
                                    </p>
                                    <p css={tw`text-2xs text-neutral-300 uppercase`}>
                                        Использован:&nbsp;
                                        {act.createdAt
                                            ? format(act.createdAt, 'MMM do, yyyy HH:mm', { locale: ru })
                                            : 'Никогда'}
                                    </p>
                                    <p css={tw`text-2xs text-neutral-300 uppercase`}>
                                        Использованный код:&nbsp;{act.code}
                                    </p>
                                </div>
                            </GreyRowBox>
                        ))
                    )}
                </ContentBox>
            </Container>
        </PageContentBlock>
    );
};
