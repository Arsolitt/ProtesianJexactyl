import { Form, Formik } from 'formik';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import { number, object, string } from 'yup';
import { Egg, getEggs } from '@/api/store/getEggs';
import createServer from '@/api/store/createServer';
import Spinner from '@/components/elements/Spinner';
import { getNests, Nest } from '@/api/store/getNests';
import { Costs, getCosts } from '@/api/store/getCosts';
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

    useEffect(() => {
        clearFlashes();
        getLimits().then((limits) => setLimits(limits));
        getCosts().then((costs) => setCosts(costs));
        getEggs().then((eggs) => setEggs(eggs));
        getNests().then((nests) => setNests(nests));
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
    };
    if (!nests || !eggs || !limits || !costs) return <Spinner size={'large'} centered />;

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
                    description: '',
                    memory: limits?.min.memory || 1024,
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
                    <Form className={'relative pb-24'}>
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
                        <p className={'text-sm text-gray-400 invisible lg:visible'}>
                            *Если нажать Shift, то можно более точно выбирать характеристики
                        </p>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Лимит ОЗУ'} icon={faMemory} className={'mt-8 sm:mt-0'}>
                                <div className={'flex'}>
                                    <Button.Text
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'memory';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 256 : 1024)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Text>
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
                                                incrementValue(field, values[field], event.shiftKey ? 256 : 1024)
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
                                    <Button.Text
                                        type={'button'}
                                        css={tw``}
                                        onClick={(event) => {
                                            const field = 'disk';
                                            setFieldValue(
                                                field,
                                                decrementValue(field, values[field], event.shiftKey ? 1024 : 5120)
                                            );
                                            setFieldTouched(field);
                                            validateField(field);
                                        }}
                                    >
                                        <Icon.Minus />
                                    </Button.Text>
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
                                                incrementValue(field, values[field], event.shiftKey ? 1024 : 5120)
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
                                    <Button.Text
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
                                    </Button.Text>
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
                                    <Button.Text
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
                                    </Button.Text>
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
                                    <Button.Text
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
                                    </Button.Text>
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
                        {calcPrice('disk', values.disk)}
                        {calcPrice('memory', values.memory)}
                        {calcPrice('backups', values.backups)}
                        {calcPrice('databases', values.databases)}
                        {calcPrice('allocations', values.allocations)}
                        {/*</StoreContainer>*/}
                        <div className={'fixed bottom-0 left-0 right-0 xl:left-36 xl:right-16'}>
                            <TitledGreyBox
                                title={'Итог'}
                                icon={faCoins}
                                className={'mt-8 sm:mt-0 text-base md:text-lg'}
                            >
                                <div className={'flex w-full items-center'}>
                                    <p className={'mr-4 inline'}>
                                        Месяц: <span className={'text-main-500'}>{prices.monthly.toFixed(2)}р.</span>
                                    </p>
                                    <p className={'mr-4 inline'}>
                                        День: <span className={'text-main-500'}>{prices.daily.toFixed(2)}р.</span>
                                    </p>
                                    <p className={'mr-4 inline'}>
                                        Час: <span className={'text-main-500'}>{prices.hourly.toFixed(2)}р.</span>
                                    </p>

                                    <div className={'ml-auto'}>
                                        <InputSpinner visible={loading}>
                                            {/*<FlashMessageRender byKey={'store:create'} className={'my-2'} />*/}
                                            <div className={'text-center'}>
                                                {user.discount > 0 && (
                                                    <p className={''}>
                                                        Скидка:{' '}
                                                        <span className={'text-main-500'}>
                                                            {user.discount.toFixed(2)}%
                                                        </span>
                                                    </p>
                                                )}
                                                <Button.Success
                                                    type={'submit'}
                                                    className={'w-32 md:w-40'}
                                                    disabled={loading || !enough || !egg || !nest}
                                                >
                                                    <Icon.Server className={'mr-2'} /> Создать
                                                </Button.Success>
                                            </div>
                                        </InputSpinner>
                                    </div>
                                </div>
                                {!enough && (
                                    <p className={'mt-1 text-xs text-negative-500'}>
                                        На балансе должно быть минимум {prices.daily.toFixed(2)}р.
                                    </p>
                                )}
                                {!nest && <p className={'mt-1 text-xs text-negative-500'}>Нужно выбрать категорию!</p>}
                                {!egg && (
                                    <p className={'mt-1 text-xs text-negative-500'}>Нужно выбрать стартовый образ!</p>
                                )}
                            </TitledGreyBox>
                        </div>
                    </Form>
                )}
            </Formik>
        </PageContentBlock>
    );
};
