import React from 'react';
import classNames from 'classnames';
import * as Icon from 'react-feather';

type Props = {
    className?: string;
};

export default (props: Props) => {
    return (
        <div className={classNames('text-lg text-gray-200', props.className)}>
            <h1 className={'text-3xl font-bold text-center'}>Просто Minecraft хостинг о_О</h1>

            <div className={'md:flex md:flex-col mt-4'}>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.PieChart className={'mr-1 inline-block'} /> Без свапа и оверсела
                </h2>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Cpu className={'mr-1 inline-block'} /> Безлимитный процессор
                </p>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Shield className={'mr-1 inline-block'} />
                    Бибос защита
                </h2>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.HelpCircle className={'mr-1 inline-block'} />
                    Крутая техподдержка
                </h2>

                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Tool className={'mr-1 inline-block'} />
                    Удобная панель
                </h2>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Sliders className={'mr-1 inline-block'} />
                    Гибкая конфигурация
                </h2>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Wifi className={'mr-1 inline-block'} />
                    Низкий пинг
                </h2>
                <h2
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.TrendingUp className={'mr-1 inline-block'} />
                    Аптайм 99.9%
                </h2>
            </div>
        </div>
    );
};
