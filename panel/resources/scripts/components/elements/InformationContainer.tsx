import useFlash from '@/plugins/useFlash';
import apiVerify from '@/api/account/verify';
import { useStoreState } from '@/state/hooks';
import React, { useEffect, useState } from 'react';
import { formatDistanceToNowStrict } from 'date-fns';
import Translate from '@/components/elements/Translate';
import InformationBox from '@/components/elements/InformationBox';
import getLatestActivity, { Activity } from '@/api/account/getLatestActivity';
import { wrapProperties } from '@/components/elements/activity/ActivityLogEntry';
import { faCoins, faScroll, faTimesCircle, faUserLock } from '@fortawesome/free-solid-svg-icons';
import { ru } from 'date-fns/locale';

export default () => {
    const { addFlash } = useFlash();
    const [activity, setActivity] = useState<Activity>();
    const properties = wrapProperties(activity?.properties);
    const user = useStoreState((state) => state.user.data!);
    // const store = useStoreState((state) => state.storefront.data!);

    useEffect(() => {
        getLatestActivity().then((activity) => setActivity(activity));
    }, []);

    const verify = () => {
        apiVerify().then((data) => {
            if (data.success)
                addFlash({ type: 'info', key: 'dashboard', message: 'Письмо с подтверждением отправлено.' });
        });
    };

    return (
        <>
            {/*{store.earn.enabled ? (*/}
            {/*    <InformationBox icon={faCircle} iconCss={'animate-pulse'}>*/}
            {/*        Earning <span className={'text-green-600'}>{store.earn.amount}</span> credits / min.*/}
            {/*    </InformationBox>*/}
            {/*) : (*/}
            {/*    <InformationBox icon={faExclamationCircle}>*/}
            {/*        Credit earning is currently <span className={'text-red-600'}>disabled.</span>*/}
            {/*    </InformationBox>*/}
            {/*)}*/}
            <InformationBox icon={faCoins}>
                Баланс: <span className={'text-green-600'}>{user.credits}</span>
            </InformationBox>
            <InformationBox icon={faUserLock}>
                {user.useTotp ? (
                    <>
                        2FA <span className={'text-green-600'}>Включена</span>
                    </>
                ) : (
                    <>
                        2FA <span className={'text-yellow-600'}>Выключена</span>
                    </>
                )}
            </InformationBox>
            {!user.verified ? (
                <InformationBox icon={faTimesCircle} iconCss={'text-yellow-500'}>
                    <span onClick={verify} className={'cursor-pointer text-blue-400 pointer-events-auto'}>
                        Подтвердите свой аккаунт
                    </span>
                </InformationBox>
            ) : (
                <InformationBox icon={faScroll}>
                    {activity ? (
                        <>
                            <span className={'text-neutral-400'}>
                                <Translate
                                    ns={'activity'}
                                    values={properties}
                                    i18nKey={activity.event.replace(':', '.')}
                                />
                            </span>
                            {' - '}
                            {formatDistanceToNowStrict(activity.timestamp, { addSuffix: true, locale: ru })}
                        </>
                    ) : (
                        'Невозможно получить журнал активности.'
                    )}
                </InformationBox>
            )}
        </>
    );
};
