import { Form, Formik } from 'formik';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import { number, object, string } from 'yup';
import { Egg, getEggs } from '@/api/store/getEggs';
import createServer from '@/api/store/createServer';
import Spinner from '@/components/elements/Spinner';
import { getNodes, Node } from '@/api/store/getNodes';
import { getNests, Nest } from '@/api/store/getNests';
import { Costs, getCosts } from '@/api/store/getCosts';
import StoreError from '@/components/elements/store/StoreError';
import React, { ChangeEvent, useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import { getLimits, Limits } from '@/api/store/getLimits';
import StoreContainer from '@/components/elements/StoreContainer';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import Field from '@/components/elements/Field';
import {
    faArchive,
    faCoins,
    faCube,
    faDatabase,
    faEgg,
    faHdd,
    faList,
    faMemory,
    faNetworkWired,
    faStickyNote,
} from '@fortawesome/free-solid-svg-icons';
import { Button } from '@/components/elements/button';
import InputSpinner from '@/components/elements/InputSpinner';
import FlashMessageRender from '@/components/FlashMessageRender';
import * as Icon from 'react-feather';
import Select from '@/components/elements/Select';
import tw from 'twin.macro';

interface CreateValues {
    name: string;
    description: string | null;

    memory: number;
    disk: number;
    allocations: number;
    backups: number;
    databases: number;

    egg: number;
    nest: number;
}

interface Prices {
    memory: number;
    disk: number;
    allocations: number;
    backups: number;
    databases: number;
    monthly: number;
    daily: number;
    hourly: number;
}

export default () => {
    const [loading, setLoading] = useState(false);
    const [enough, setEnough] = useState(false);
    const [limits, setLimits] = useState<Limits>({
        min: {
            memory: 0,
            disk: 0,
            allocations: 0,
            backups: 0,
            databases: 0,
        },
        max: {
            memory: 0,
            disk: 0,
            allocations: 0,
            backups: 0,
            databases: 0,
        },
    });
    const [costs, setCosts] = useState<Costs>({
        memory: 0,
        disk: 0,
        allocations: 0,
        backups: 0,
        databases: 0,
        slots: 0,
    });
    const prices: Prices = {
        memory: 0,
        disk: 0,
        allocations: 0,
        backups: 0,
        databases: 0,
        monthly: 0,
        daily: 0,
        hourly: 0,
    };

    const user = useStoreState((state) => state.user.data!);
    const discount = 1 - user.discount / 100;
    const { clearFlashes, clearAndAddHttpError } = useFlash();

    const [egg, setEgg] = useState<number>(0);
    const [eggs, setEggs] = useState<Egg[]>();
    const [nest, setNest] = useState<number>(0);
    const [nests, setNests] = useState<Nest[]>();
    const [nodes, setNodes] = useState<Node[]>();

    useEffect(() => {
        clearFlashes();

        getLimits().then((limits) => setLimits(limits));
        getCosts().then((costs) => setCosts(costs));

        getEggs().then((eggs) => setEggs(eggs));
        getNests().then((nests) => setNests(nests));
        getNodes().then((nodes) => setNodes(nodes));
    }, []);

    const submit = (values: CreateValues) => {
        setLoading(true);
        clearFlashes('store:create');

        createServer(values, egg, nest)
            .then((data) => {
                if (!data.id) return;

                setLoading(false);
                clearFlashes('store:create');
                // @ts-expect-error this is valid
                window.location = `/server/${data.id}`;
            })
            .catch((error) => {
                setLoading(false);
                clearAndAddHttpError({ key: 'store:create', error });
            });
    };

    const changeNest = (e: ChangeEvent<HTMLSelectElement>) => {
        setNest(parseInt(e.target.value));

        getEggs(parseInt(e.target.value)).then((eggs) => {
            setEggs(eggs);
            setEgg(eggs[0].id);
        });
    };

    const totalPrice = () => {
        prices.monthly = prices.memory + prices.disk + prices.allocations + prices.backups + prices.databases;
        prices.daily = prices.monthly / 30;
        prices.hourly = prices.monthly / 30 / 24;
        setEnough(prices.daily <= user.credits);
    };

    const calcPrice = <T extends keyof Prices & keyof Costs>(field: T, amount: number) => {
        prices[field] = amount * costs[field] * discount;
        totalPrice();
        return prices[field].toFixed();
    };

    if (!costs) return <Spinner size={'large'} centered />;
    if (!limits) return <Spinner size={'large'} centered />;

    if (!nodes) {
        return (
            <StoreError
                message={'Нет доступных серверов. Попробуй позже, или стучись в службу поддержки!'}
                admin={'Ensure you have at least one node that can be deployed to.'}
            />
        );
    }

    if (!nests || !eggs) {
        return (
            <StoreError
                message={'Нет доступных серверов. Попробуй позже, или стучись в службу поддержки!'}
                admin={'Ensure you have at least one egg which is in a public nest.'}
            />
        );
    }

    const incrementValue = (field: keyof Limits['max'], oldValue: number, amount: number): number => {
        return Number(oldValue) + amount <= limits.max[field] ? Number(oldValue) + amount : limits.max[field];
    };

    const decrementValue = (field: keyof Limits['min'], oldValue: number, amount: number): number => {
        return Number(oldValue) - amount >= limits.min[field] ? Number(oldValue) - amount : limits.min[field];
    };

    // TODO: потом это обязательно переделать почище, пока так сойдёт

    return (
        <PageContentBlock title={'Создать сервер'} showFlashKey={'store:create'}>
            <Formik
                onSubmit={submit}
                initialValues={{
                    name: `Крутой сервер от ${user.username} 0_0`,
                    description: 'Описание вот сюда',
                    memory: limits?.min.memory || 512,
                    disk: limits?.min.disk || 1024,
                    allocations: limits?.min.allocations || 1,
                    backups: limits?.min.backups || 0,
                    databases: limits?.min.databases || 0,
                    nest: 1,
                    egg: 1,
                }}
                validationSchema={object().shape({
                    name: string().required().min(3),
                    description: string().optional().min(3).max(255),

                    memory: number().required().min(limits.min.memory).max(limits.max.memory),
                    disk: number().required().min(limits.min.disk).max(limits.max.disk),

                    allocations: number().required().min(limits.min.allocations).max(limits.max.allocations),
                    backups: number().required().min(limits.min.backups).max(limits.max.backups),
                    databases: number().required().min(limits.min.databases).max(limits.max.databases),

                    nest: number().required().default(nest),
                    egg: number().required().default(egg),
                })}
            >
                {({ values, setFieldValue, validateField, setFieldTouched }) => (
                    <Form>
                        <h1 className={'text-5xl'}>Основная информация</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Назови свой сервер и придумай ему описание</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-2 my-10 gap-4'}>
                            <TitledGreyBox title={'Название сервера'} icon={faStickyNote} className={'mt-8 sm:mt-0'}>
                                <Field name={'name'} />
                                <p className={'mt-1 text-xs'}>Название твоего сервера</p>
                                <p className={'mt-1 text-xs text-gray-400'}>
                                    Допустимые символы: <code>а-я А-Я a-z A-Z 0-9 _ - .</code> и <code>[Пробелы]</code>.
                                </p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Описание сервера'} icon={faList} className={'mt-8 sm:mt-0'}>
                                <Field name={'description'} />
                                <p className={'mt-1 text-xs'}>Смешные названия и описания приветствуются</p>
                                <p className={'mt-1 text-xs text-yellow-400'}>* Необязательно</p>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Характеристики</h1>
                        <h3 className={'text-2xl text-neutral-500'}>
                            Выбери желаемое количество ОЗУ и дискового пространства.
                        </h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Лимит ОЗУ'} icon={faMemory} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Danger
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'memory';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 1024 : 256)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Danger>
                                    <div className={'relative mx-2 w-full'}>
                                        <Field name={'memory'} className={'text-center'} />
                                        <p className={'absolute text-sm top-1 right-2 bg-gray-700 p-2 rounded-lg'}>
                                            МБ
                                        </p>
                                    </div>
                                    <Button.Success
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'memory';
                                            setFieldValue(
                                                field,
                                                incrementValue(field, values[field], event.shiftKey ? 1024 : 256)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Plus />
                                    </Button.Success>
                                </div>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Лимит диска'} icon={faHdd} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Danger
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'disk';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 5120 : 1024)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Danger>
                                    <div className={'relative mx-2 w-full'}>
                                        <Field name={'disk'} className={'text-center'} />
                                        <p className={'absolute text-sm top-1 right-2 bg-gray-700 p-2 rounded-lg'}>
                                            МБ
                                        </p>
                                    </div>
                                    <Button.Success
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'disk';
                                            setFieldValue(
                                                field,
                                                incrementValue(field, values[field], event.shiftKey ? 5120 : 1024)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Plus />
                                    </Button.Success>
                                </div>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Дополнительные ништяки</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Базы данных, порты, резервные копии</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Резервные копии'} icon={faArchive} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Danger
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'backups';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Danger>
                                    <div className={'relative mx-2 w-full'}>
                                        <Field name={'backups'} className={'text-center'} />
                                        <p className={'absolute text-sm top-1 right-2 bg-gray-700 p-2 rounded-lg'}>
                                            Шт.
                                        </p>
                                    </div>
                                    <Button.Success
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'backups';
                                            setFieldValue(
                                                field,
                                                incrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Plus />
                                    </Button.Success>
                                </div>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Базы данных'} icon={faDatabase} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Danger
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'databases';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Danger>
                                    <div className={'relative mx-2 w-full'}>
                                        <Field name={'databases'} className={'text-center'} />
                                        <p className={'absolute text-sm top-1 right-2 bg-gray-700 p-2 rounded-lg'}>
                                            Шт.
                                        </p>
                                    </div>
                                    <Button.Success
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'databases';
                                            setFieldValue(
                                                field,
                                                incrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Plus />
                                    </Button.Success>
                                </div>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Порты'} icon={faNetworkWired} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Danger
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'allocations';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Danger>
                                    <div className={'relative mx-2 w-full'}>
                                        <Field name={'allocations'} className={'text-center'} />
                                        <p className={'absolute text-sm top-1 right-2 bg-gray-700 p-2 rounded-lg'}>
                                            Шт.
                                        </p>
                                    </div>
                                    <Button.Success
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'allocations';
                                            setFieldValue(
                                                field,
                                                incrementValue(field, values[field], event.shiftKey ? 5 : 1)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Plus />
                                    </Button.Success>
                                </div>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Что будешь запускать?</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Выбери ПО, которое хочешь запускать на сервере</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Категория'} icon={faCube} className={'mt-8 sm:mt-0'}>
                                <Select name={'nest'} onChange={(nest) => changeNest(nest)}>
                                    {!nest && (
                                        <option selected={true} disabled={true}>
                                            Выбери категорию...
                                        </option>
                                    )}
                                    {nests.map((n) => (
                                        <option key={n.id} value={n.id}>
                                            {n.name}
                                        </option>
                                    ))}
                                </Select>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Исходный образ'} icon={faEgg} className={'mt-8 sm:mt-0'}>
                                <Select name={'egg'} onChange={(e) => setEgg(parseInt(e.target.value))}>
                                    {!egg && (
                                        <option selected={true} disabled={true}>
                                            Выбери исходный образ...
                                        </option>
                                    )}
                                    {nest &&
                                        eggs.map((e) => (
                                            <option key={e.id} value={e.id}>
                                                {e.name}
                                            </option>
                                        ))}
                                </Select>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Сводка</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Давай посмотрим, что ты тут собрал</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox
                                title={'Итоговые характеристики'}
                                icon={faList}
                                className={'mt-8 sm:mt-0 text-lg'}
                            >
                                <p className={'mt-1'}>
                                    Дисковое пространство: {values.disk} МБ |{' '}
                                    <span className={'text-green-500'}>{calcPrice('disk', values.disk)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Оперативная память: {values.memory} МБ |{' '}
                                    <span className={'text-green-500'}>{calcPrice('memory', values.memory)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Резервные копии: {values.backups} Шт. |{' '}
                                    <span className={'text-green-500'}>{calcPrice('backups', values.backups)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Базы данных: {values.databases} Шт. |{' '}
                                    <span className={'text-green-500'}>
                                        {calcPrice('databases', values.databases)}р.
                                    </span>
                                </p>
                                <p className={'mt-1'}>
                                    Порты: {values.allocations} Шт. |{' '}
                                    <span className={'text-green-500'}>
                                        {calcPrice('allocations', values.allocations)}р.
                                    </span>
                                </p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Цены'} icon={faCoins} className={'mt-8 sm:mt-0 text-lg'}>
                                <p className={'mt-1'}>
                                    Твоя скидка: <span className={'text-green-500'}>{user.discount.toFixed(2)}%</span>
                                </p>
                                <p className={'mt-1'}>
                                    В месяц: <span className={'text-green-500'}>{prices.monthly.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    В день: <span className={'text-green-500'}>{prices.daily.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    В час: <span className={'text-green-500'}>{prices.hourly.toFixed(2)}р.</span>
                                </p>
                                {!enough && (
                                    <p className={'mt-1 text-xs text-red-400'}>
                                        На балансе должно быть минимум {prices.daily.toFixed(2)}р.
                                    </p>
                                )}
                                {!nest && <p className={'mt-1 text-xs text-red-400'}>Нужно выбрать категорию!</p>}
                                {!egg && <p className={'mt-1 text-xs text-red-400'}>Нужно выбрать стартовый образ!</p>}
                            </TitledGreyBox>
                        </StoreContainer>
                        <InputSpinner visible={loading}>
                            <FlashMessageRender byKey={'store:create'} className={'my-2'} />
                            <div className={'text-right'}>
                                <Button
                                    type={'submit'}
                                    className={'w-1/6 mb-4'}
                                    size={Button.Sizes.Large}
                                    disabled={loading || !enough || !egg || !nest}
                                >
                                    <Icon.Server className={'mr-2'} /> Поехали!
                                </Button>
                            </div>
                        </InputSpinner>
                    </Form>
                )}
            </Formik>
        </PageContentBlock>
    );
};
