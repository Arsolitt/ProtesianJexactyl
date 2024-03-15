import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import * as Icon from 'react-feather';
import React, { useEffect, useState } from 'react';
import useFlash from '@/plugins/useFlash';
import styled from 'styled-components/macro';
import { ServerContext } from '@/state/server';
import { Dialog } from '@/components/elements/dialog';
import { Button } from '@/components/elements/button/index';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import editServer, { Resources } from '@/api/server/editServer';
import { Costs, getCosts } from '@/api/store/getCosts';
import { useStoreState } from 'easy-peasy';
import { getLimits, Limits } from '@/api/store/getLimits';

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

const Wrapper = styled.div`
    ${tw`text-2xl flex flex-row justify-center items-center`};
`;

interface Prices {
    monthly: number;
    daily: number;
    hourly: number;
}

export default () => {
    const [submitting, setSubmitting] = useState(false);
    const [isEqual, setIsEqual] = useState(true);
    const [isEnoughCredits, setIsEnoughCredits] = useState(true);

    const user = useStoreState((state) => state.user.data!);
    const [costs, setCosts] = useState<Costs>();
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const monthlyPrice = ServerContext.useStoreState((state) => state.server.data!.monthlyPrice);
    const currentResources = ServerContext.useStoreState((state) => state.server.data!.limits);
    const currentFeatures = ServerContext.useStoreState((state) => state.server.data!.featureLimits);
    const { clearFlashes, addFlash, clearAndAddHttpError } = useFlash();
    const [limits, setLimits] = useState<Limits>();

    useEffect(() => {
        clearFlashes();
        getCosts().then((costs: Costs) => setCosts(costs));
        getLimits().then((limits: Limits) => setLimits(limits));
    }, []);

    const [resourcesInitialState, setResourcesInitialState] = useState<Resources>({
        memory: currentResources?.memory,
        disk: currentResources?.disk,
        allocations: currentFeatures?.allocations,
        backups: currentFeatures?.backups,
        databases: currentFeatures?.databases,
    });
    const [resources, setResources] = useState<Resources>(resourcesInitialState);
    const compareResources = (a: Resources, b: Resources) => {
        return (
            a.memory === b.memory &&
            a.disk === b.disk &&
            a.allocations === b.allocations &&
            a.backups === b.backups &&
            a.databases === b.databases
        );
    };

    const resourceMinLimits: Resources = {
        memory: limits?.min.memory || 512,
        disk: limits?.min.disk || 1536,
        allocations: limits?.min.allocations || 1,
        backups: limits?.min.backups || 0,
        databases: limits?.min.databases || 0,
    };

    const resourceMaxLimits: Resources = {
        memory: limits?.max.memory || 102400,
        disk: limits?.max.disk || 1024000,
        allocations: limits?.max.allocations || 999,
        backups: limits?.max.backups || 999,
        databases: limits?.max.databases || 999,
    };

    const increment = (field: keyof Resources, amount: number) => {
        if (Number(resources[field]) === Number(resourceMaxLimits[field])) {
            return;
        }
        setResources((prevState) => ({
            ...prevState,
            [field]:
                Number(prevState[field]) + amount < Number(resourceMaxLimits[field])
                    ? Number(prevState[field]) + amount
                    : Number(resourceMaxLimits[field]),
        }));
    };

    const decrement = (field: keyof Resources, amount: number) => {
        if (Number(resources[field]) === Number(resourceMinLimits[field])) {
            return;
        }
        setResources((prevState) => ({
            ...prevState,
            [field]:
                Number(prevState[field]) - amount > Number(resourceMinLimits[field])
                    ? Number(prevState[field]) - amount
                    : Number(resourceMinLimits[field]),
        }));
    };

    const finalPrices = (): Prices => {
        if (!costs) {
            return { monthly: 0, daily: 0, hourly: 0 };
        }
        const discount = 1 - user.discount / 100;
        const data = {
            memory: resources.memory * costs.memory,
            disk: resources.disk * costs.disk,
            ports: resources.allocations * costs.allocations,
            backups: resources.backups * costs.backups,
            databases: resources.databases * costs.databases,
        };
        const price: number = (data.memory + data.disk + data.ports + data.backups + data.databases) * discount;
        return {
            monthly: price,
            daily: price / 30,
            hourly: price / 30 / 24,
        };
    };

    useEffect(() => {
        setIsEqual(compareResources(resources, resourcesInitialState));
        setIsEnoughCredits(user.credits >= finalPrices().daily);
    }, [resources, resourcesInitialState, user.credits]);

    const edit = () => {
        clearFlashes('server:edit');
        setSubmitting(true);

        editServer(uuid, resources)
            .then(() => {
                addFlash({
                    key: 'server:edit',
                    type: 'success',
                    message: 'Характеристики сервера успешно изменены!',
                });
                setResourcesInitialState(resources);
                user.credits = user.credits - finalPrices().hourly;
            })
            .catch((error) => clearAndAddHttpError({ key: 'server:edit', error }));
        setSubmitting(false);
    };

    return (
        <ServerContentBlock
            title={'Характеристики сервера'}
            description={'Изменить доступные серверу ресурсы'}
            showFlashKey={'server:edit'}
        >
            <SpinnerOverlay size={'large'} visible={submitting} />
            <Dialog.Confirm
                confirm={'Изменить'}
                open={submitting}
                onClose={() => setSubmitting(false)}
                title={'Подтвердить изменение характеристик'}
                onConfirmed={() => edit()}
            >
                Характеристики сервера будут изменены и спишется оплата за первый час работы.
            </Dialog.Confirm>
            <Container className={'md:!grid md:!grid-cols-3 gap-4 my-10'}>
                <TitledGreyBox title={'Оперативная память'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <Icon.PieChart size={40} />
                        <Button.Text
                            className={'ml-4'}
                            onClick={(event) => {
                                decrement('memory', event.shiftKey ? 1024 : 256);
                            }}
                            disabled={resources.memory <= resourceMinLimits.memory}
                        >
                            <Icon.Minus />
                        </Button.Text>
                        <Button.Success
                            className={'ml-4'}
                            onClick={(event) => {
                                increment('memory', event.shiftKey ? 1024 : 256);
                            }}
                            disabled={resources.memory >= resourceMaxLimits.memory}
                        >
                            <Icon.Plus />
                        </Button.Success>
                    </Wrapper>
                    <p className={'mt-2 text-gray-200 flex justify-center'}>{resources.memory} МБ</p>
                    <p className={'mt-1 text-gray-500 text-xs flex justify-center'}>
                        Ограничения: от {resourceMinLimits.memory} МБ до {resourceMaxLimits.memory} МБ
                    </p>
                </TitledGreyBox>
                <TitledGreyBox title={'Дисковое пространство'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <Icon.HardDrive size={40} />
                        <Button.Text
                            className={'ml-4'}
                            onClick={(event) => {
                                decrement('disk', event.shiftKey ? 5120 : 1024);
                            }}
                            disabled={resources.disk <= resourceMinLimits.disk}
                        >
                            <Icon.Minus />
                        </Button.Text>
                        <Button.Success
                            className={'ml-4'}
                            onClick={(event) => {
                                increment('disk', event.shiftKey ? 5120 : 1024);
                            }}
                            disabled={resources.disk >= resourceMaxLimits.disk}
                        >
                            <Icon.Plus />
                        </Button.Success>
                    </Wrapper>
                    <p className={'mt-2 text-gray-200 flex justify-center'}>{resources.disk} МБ</p>
                    <p className={'mt-1 text-gray-500 text-xs flex justify-center'}>
                        Ограничения: от {resourceMinLimits.disk} МБ до {resourceMaxLimits.disk} МБ
                    </p>
                </TitledGreyBox>
                <TitledGreyBox title={'Доступные порты'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <Icon.Share2 size={40} />
                        <Button.Text
                            className={'ml-4'}
                            onClick={(event) => {
                                decrement('allocations', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.allocations <= resourceMinLimits.allocations}
                        >
                            <Icon.Minus />
                        </Button.Text>
                        <Button.Success
                            className={'ml-4'}
                            onClick={(event) => {
                                increment('allocations', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.allocations >= resourceMaxLimits.allocations}
                        >
                            <Icon.Plus />
                        </Button.Success>
                    </Wrapper>
                    <p className={'mt-2 text-gray-200 flex justify-center'}>{resources.allocations} шт.</p>
                    <p className={'mt-1 text-gray-500 text-xs flex justify-center'}>
                        Ограничения: от {resourceMinLimits.allocations} шт. до {resourceMaxLimits.allocations} шт.
                    </p>
                </TitledGreyBox>
                <TitledGreyBox title={'Количество бэкапов'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <Icon.Archive size={40} />
                        <Button.Text
                            className={'ml-4'}
                            onClick={(event) => {
                                decrement('backups', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.backups <= resourceMinLimits.backups}
                        >
                            <Icon.Minus />
                        </Button.Text>
                        <Button.Success
                            className={'ml-4'}
                            onClick={(event) => {
                                increment('backups', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.backups >= resourceMaxLimits.backups}
                        >
                            <Icon.Plus />
                        </Button.Success>
                    </Wrapper>
                    <p className={'mt-2 text-gray-200 flex justify-center'}>{resources.backups} шт.</p>
                    <p className={'mt-1 text-gray-500 text-xs flex justify-center'}>
                        Ограничения: от {resourceMinLimits.backups} шт. до {resourceMaxLimits.backups} шт.
                    </p>
                </TitledGreyBox>
                <TitledGreyBox title={'Количество баз данных'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <Icon.Database size={40} />
                        <Button.Text
                            className={'ml-4'}
                            onClick={(event) => {
                                decrement('databases', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.databases <= resourceMinLimits.databases}
                        >
                            <Icon.Minus />
                        </Button.Text>
                        <Button.Success
                            className={'ml-4'}
                            onClick={(event) => {
                                increment('databases', event.shiftKey ? 5 : 1);
                            }}
                            disabled={resources.databases >= resourceMaxLimits.databases}
                        >
                            <Icon.Plus />
                        </Button.Success>
                    </Wrapper>
                    <p className={'mt-2 text-gray-200 flex justify-center'}>{resources.databases} шт.</p>
                    <p className={'mt-1 text-gray-500 text-xs flex justify-center'}>
                        Ограничения: от {resourceMinLimits.databases} шт. до {resourceMaxLimits.databases} шт.
                    </p>
                </TitledGreyBox>
                <TitledGreyBox title={'Итог'} className={'mt-8 sm:mt-0'}>
                    <Wrapper>
                        <div className={'flex justify-around w-full'}>
                            <div className={'old-price flex flex-col items-center'}>
                                <p className={'mt-2 text-gray-200 text-sm md:text-xl'}>
                                    Было:{' '}
                                    <span className={'text-sm text-gray-300'}>{monthlyPrice.toFixed(2)}₽/месяц</span>
                                </p>
                                <Button.Text
                                    onClick={() => {
                                        setResources(resourcesInitialState);
                                        setTimeout(() => setIsEnoughCredits(true), 50);
                                    }}
                                >
                                    <Icon.RotateCcw />
                                    Сбросить
                                </Button.Text>
                            </div>
                            <div className={'new-price flex flex-col items-center'}>
                                <p className={'mt-2 text-gray-200 text-sm md:text-xl'}>
                                    Стало:{' '}
                                    <span className={'text-sm text-gray-300'}>
                                        {finalPrices().monthly.toFixed(2)}₽/месяц
                                    </span>
                                </p>
                                <Button.Success
                                    onClick={() => {
                                        setSubmitting(true);
                                    }}
                                    disabled={isEqual || !isEnoughCredits}
                                >
                                    <Icon.Check />
                                    Изменить
                                </Button.Success>
                            </div>
                        </div>
                    </Wrapper>
                    {!isEnoughCredits && (
                        <p className={'mt-1 text-xs text-red-400'}>
                            На балансе должно быть минимум {finalPrices().daily.toFixed(2)} ₽
                        </p>
                    )}
                </TitledGreyBox>
            </Container>
        </ServerContentBlock>
    );
};
