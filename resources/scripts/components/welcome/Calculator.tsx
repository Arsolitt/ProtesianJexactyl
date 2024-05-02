import React, { useState } from 'react';
import classNames from 'classnames';
import * as Icon from 'react-feather';

type Props = {
    className?: string;
};

export default (props: Props) => {
    const minMemory = 512;
    const maxMemory = 16384;
    const minDisk = 1024;
    const maxDisk = 102400;
    const priceMemory = 55 / 1024;
    const priceDisk = 4 / 1024;
    const [valueMemory, setValueMemory] = useState(minMemory);
    const [valueDisk, setValueDisk] = useState(minDisk);

    return (
        <div className={classNames('', props.className)}>
            <h2 className={'text-3xl font-bold text-center'}>Рассчитай стоимость!</h2>
            <div className={'lg:flex gap-2'}>
                <div className={'border-main-500 border-2 rounded-xl px-4 py-2 my-3 lg:w-1/2 block'}>
                    <div className={'flex text-lg'}>
                        <span className={'whitespace-nowrap'}>{minMemory} МБ</span>
                        <input
                            className={'w-full mx-2'}
                            type='range'
                            min={minMemory}
                            max={maxMemory}
                            step={256}
                            value={valueMemory}
                            onChange={(event) => setValueMemory(parseInt(event.target.value))}
                        />
                        <span className={'whitespace-nowrap'}>{maxMemory} МБ</span>
                    </div>
                    <p className={'text-center'}>
                        <Icon.PieChart className={'mr-1 inline-block text-warning-500'} />
                        {valueMemory} МБ
                    </p>
                </div>

                <div className={'border-main-500 border-2 rounded-xl px-4 py-2 my-3 lg:w-1/2 block'}>
                    <div className={'flex text-lg'}>
                        <span className={'whitespace-nowrap'}>{minDisk} МБ</span>
                        <input
                            className={'w-full mx-2'}
                            type='range'
                            min={minDisk}
                            max={maxDisk}
                            step={1024}
                            value={valueDisk}
                            onChange={(event) => setValueDisk(parseInt(event.target.value))}
                        />
                        <span className={'whitespace-nowrap'}>{maxDisk} МБ</span>
                    </div>
                    <p className={'text-center'}>
                        <Icon.HardDrive className={'mr-1 inline-block text-warning-500'} />
                        {valueDisk} МБ
                    </p>
                </div>
            </div>
            <div className={'flex justify-evenly'}>
                <p className={'flex flex-col text-lg'}>
                    <span>{((valueMemory * priceMemory + valueDisk * priceDisk) / 30 / 24).toFixed(2)}р.</span> В час
                </p>
                <p className={'flex flex-col text-lg'}>
                    <span>{((valueMemory * priceMemory + valueDisk * priceDisk) / 30).toFixed(2)}р.</span> В день
                </p>
                <p className={'flex flex-col text-lg'}>
                    <span>{(valueMemory * priceMemory + valueDisk * priceDisk).toFixed(2)}р.</span> В месяц
                </p>
            </div>
        </div>
    );
};
