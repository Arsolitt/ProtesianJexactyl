import * as Icon from 'react-feather';
import { Form, Formik } from 'formik';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import { number, object, string } from 'yup';
import Field from '@/components/elements/Field';
import Select from '@/components/elements/Select';
import { Egg, getEggs } from '@/api/store/getEggs';
import createServer from '@/api/store/createServer';
import Spinner from '@/components/elements/Spinner';
import { getNodes, Node } from '@/api/store/getNodes';
import { getNests, Nest } from '@/api/store/getNests';
import { Costs, getCosts } from '@/api/store/getCosts';
import { Button } from '@/components/elements/button';
import InputSpinner from '@/components/elements/InputSpinner';
import StoreError from '@/components/elements/store/StoreError';
import React, { ChangeEvent, useEffect, useState } from 'react';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import FlashMessageRender from '@/components/FlashMessageRender';
import StoreContainer from '@/components/elements/StoreContainer';
import PageContentBlock from '@/components/elements/PageContentBlock';
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
import { getLimits, Limits } from '@/api/store/getLimits';

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
    total: number;
}

export default () => {
    const [loading, setLoading] = useState(false);
    const [limits, setLimits] = useState<Limits>();
    const [costs, setCosts] = useState<Costs>();

    const user = useStoreState((state) => state.user.data!);
    const discount = 1 - user.discount / 100;
    const { clearFlashes, clearAndAddHttpError } = useFlash();

    const [egg, setEgg] = useState<number>(0);
    const [eggs, setEggs] = useState<Egg[]>();
    const [nest, setNest] = useState<number>(0);
    const [nests, setNests] = useState<Nest[]>();
    const [nodes, setNodes] = useState<Node[]>();

    const [values, setValues] = useState<CreateValues>({
        name: `Крутой сервер от ${user.username} 0_0`,
        description: 'Напиши его прямо сюда',
        memory: limits?.min.memory || 512,
        disk: limits?.min.disk || 1536,
        allocations: limits?.min.allocations || 1,
        backups: limits?.min.backups || 0,
        databases: limits?.min.databases || 0,
        nest: 1,
        egg: 1,
    });

    useEffect(() => {
        clearFlashes();

        getLimits()
            .then((limits) => setLimits(limits))
            .catch((error) => {
                clearAndAddHttpError({ key: 'store:create', error });
                return <Spinner size={'large'} centered />;
            });
        getCosts()
            .then((costs) => setCosts(costs))
            .catch((error) => {
                clearAndAddHttpError({ key: 'store:create', error });
                return <Spinner size={'large'} centered />;
            });

        getEggs().then((eggs) => setEggs(eggs));
        getNests().then((nests) => setNests(nests));
        getNodes().then((nodes) => setNodes(nodes));
    }, []);

    if (!costs) return <Spinner size={'large'} centered />;
    if (!limits) return <Spinner size={'large'} centered />;

    const changeNest = (e: ChangeEvent<HTMLSelectElement>) => {
        setNest(parseInt(e.target.value));

        getEggs(parseInt(e.target.value)).then((eggs) => {
            setEggs(eggs);
            setEgg(eggs[0].id);
        });
    };

    const finalPrices = (): Prices => {
        const data: Prices = {
            memory: values.memory * costs.memory * discount,
            disk: values.disk * costs.disk * discount,
            allocations: (values.allocations - 1) * costs.allocations * discount,
            backups: values.backups * costs.backups * discount,
            databases: values.databases * costs.databases * discount,
            total: 0,
        };
        data.total = (data.memory + data.disk + data.allocations + data.backups + data.databases) * discount;
        console.log(values);
        return data;
    };

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

    return (
        <PageContentBlock title={'Создать сервер'} showFlashKey={'store:create'}>
            <Formik
                onSubmit={submit}
                initialValues={values}
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
                {({ values }) => (
                    <Form onChange={() => setValues(values)}>
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
                                <div className={'relative'}>
                                    <Field name={'memory'} />
                                    <p className={'absolute text-sm top-1.5 right-2 bg-gray-700 p-2 rounded-lg'}>МБ</p>
                                </div>
                                <p className={'mt-1 text-xs'}>Сколько тебе нужно ОЗУ?</p>
                                <p className={'mt-1 text-xs text-gray-400'}>???</p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Лимит диска'} icon={faHdd} className={'mt-8 sm:mt-0'}>
                                <div className={'relative'}>
                                    <Field name={'disk'} />
                                    <p className={'absolute text-sm top-1.5 right-2 bg-gray-700 p-2 rounded-lg'}>МБ</p>
                                </div>
                                <p className={'mt-1 text-xs'}>Сколько тебе нужно диска?</p>
                                <p className={'mt-1 text-xs text-gray-400'}>???</p>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Дополнительные ништяки</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Базы данных, порты, резервные копии</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Резервные копии'} icon={faArchive} className={'mt-8 sm:mt-0'}>
                                <Field name={'backups'} />
                                <p className={'mt-1 text-xs'}>Сколько тебе нужно резервных копий?</p>
                                <p className={'mt-1 text-xs text-gray-400'}>???</p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Базы данных'} icon={faDatabase} className={'mt-8 sm:mt-0'}>
                                <Field name={'databases'} />
                                <p className={'mt-1 text-xs'}>Сколько тебе нужно баз данных?</p>
                                <p className={'mt-1 text-xs text-gray-400'}>???</p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Порты'} icon={faNetworkWired} className={'mt-8 sm:mt-0'}>
                                <Field name={'allocations'} />
                                <p className={'mt-1 text-xs'}>Сколько тебе нужно портов?</p>
                                <p className={'mt-1 text-xs text-gray-400'}>???</p>
                            </TitledGreyBox>
                        </StoreContainer>
                        <h1 className={'text-5xl'}>Что будешь запускать?</h1>
                        <h3 className={'text-2xl text-neutral-500'}>Выбери ПО, которое хочешь запускать на сервере</h3>
                        <StoreContainer className={'lg:grid lg:grid-cols-3 my-10 gap-4'}>
                            <TitledGreyBox title={'Категория'} icon={faCube} className={'mt-8 sm:mt-0'}>
                                <Select name={'nest'} onChange={(nest) => changeNest(nest)}>
                                    {!nest && <option>Выбери категорию...</option>}
                                    {nests.map((n) => (
                                        <option key={n.id} value={n.id}>
                                            {n.name}
                                        </option>
                                    ))}
                                </Select>
                                <p className={'mt-1 text-xs text-gray-400'}>Выбери категорию ПО для своего сервера</p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Исходный образ'} icon={faEgg} className={'mt-8 sm:mt-0'}>
                                <Select name={'egg'} onChange={(e) => setEgg(parseInt(e.target.value))}>
                                    {!egg && <option>Выбери исходный образ...</option>}
                                    {eggs.map((e) => (
                                        <option key={e.id} value={e.id}>
                                            {e.name}
                                        </option>
                                    ))}
                                </Select>
                                <p className={'mt-1 text-xs text-gray-400'}>Выбери исходный образ для своего сервера</p>
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
                                    Оперативная память: {values.memory} МБ - это{' '}
                                    <span className={'text-green-500'}>{finalPrices().memory.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Место на диске: {values.disk} МБ - это{' '}
                                    <span className={'text-green-500'}>{finalPrices().disk.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Резервные копии: {values.backups} Шт. и это{' '}
                                    <span className={'text-green-500'}>{finalPrices().backups.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Базы данных: {values.databases} Шт. - это{' '}
                                    <span className={'text-green-500'}>{finalPrices().databases.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    Порты: {values.allocations} Шт. - это{' '}
                                    <span className={'text-green-500'}>{finalPrices().allocations.toFixed(2)}р.</span>
                                </p>
                            </TitledGreyBox>
                            <TitledGreyBox title={'Цены'} icon={faCoins} className={'mt-8 sm:mt-0 text-lg'}>
                                <p className={'mt-1'}>
                                    Твоя персональная скидка:{' '}
                                    <span className={'text-green-500'}>{user.discount.toFixed(2)}%</span>
                                </p>
                                <p className={'mt-1'}>
                                    В месяц:{' '}
                                    <span className={'text-green-500'}>{finalPrices().total.toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    В день:{' '}
                                    <span className={'text-green-500'}>{(finalPrices().total / 30).toFixed(2)}р.</span>
                                </p>
                                <p className={'mt-1'}>
                                    В час:{' '}
                                    <span className={'text-green-500'}>
                                        {(finalPrices().total / 30 / 24).toFixed(2)}р.
                                    </span>
                                </p>
                            </TitledGreyBox>
                        </StoreContainer>
                        <InputSpinner visible={loading}>
                            <FlashMessageRender byKey={'store:create'} className={'my-2'} />
                            <div className={'text-right'}>
                                <Button
                                    type={'submit'}
                                    className={'w-1/6 mb-4'}
                                    size={Button.Sizes.Large}
                                    disabled={loading}
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
