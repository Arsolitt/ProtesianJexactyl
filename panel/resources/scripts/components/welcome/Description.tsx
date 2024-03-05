import React from 'react';
import classNames from 'classnames';
import * as Icon from 'react-feather';

type Props = {
    className?: string;
};

export default (props: Props) => {
    return (
        <div className={classNames('text-lg text-gray-200', props.className)}>
            <p className={'text-3xl font-bold text-center'}>Просто Minecraft хостинг о_О</p>

            <div className={'md:flex md:flex-col mt-4'}>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.PieChart className={'mr-1 inline-block'} /> Без свапа и оверсела.
                </p>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Cpu className={'mr-1 inline-block'} /> Безлимитный процессор!
                </p>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.Shield className={'mr-1 inline-block'} />
                    Бибос защита.
                </p>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.HelpCircle className={'mr-1 inline-block'} />
                    Крутая техподдержка!
                </p>
                <p
                    className={
                        'w-fit inline-block whitespace-nowrap border-main-500 border-2 rounded-xl px-4 py-2 my-1 mr-2'
                    }
                >
                    <Icon.TrendingUp className={'mr-1 inline-block'} />
                    Аптайм 99.9%
                </p>
            </div>
        </div>
    );
};
