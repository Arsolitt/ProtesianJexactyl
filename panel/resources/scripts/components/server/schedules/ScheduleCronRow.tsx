import React from 'react';
import { Schedule } from '@/api/server/schedules/getServerSchedules';
import classNames from 'classnames';

interface Props {
    cron: Schedule['cron'];
    className?: string;
}

const ScheduleCronRow = ({ cron, className }: Props) => (
    <div className={classNames('flex', className)}>
        <div className={'w-1/5 sm:w-auto text-center'}>
            <p className={'font-medium'}>{cron.minute}</p>
            <p className={'text-2xs text-neutral-500 uppercase'}>Минуты</p>
        </div>
        <div className={'w-1/5 sm:w-auto text-center ml-4'}>
            <p className={'font-medium'}>{cron.hour}</p>
            <p className={'text-2xs text-neutral-500 uppercase'}>Часы</p>
        </div>
        <div className={'w-1/5 sm:w-auto text-center ml-4'}>
            <p className={'font-medium'}>{cron.dayOfMonth}</p>
            <p className={'text-2xs text-neutral-500 uppercase'}>День месяца</p>
        </div>
        <div className={'w-1/5 sm:w-auto text-center ml-4'}>
            <p className={'font-medium'}>{cron.month}</p>
            <p className={'text-2xs text-neutral-500 uppercase'}>Месяц</p>
        </div>
        <div className={'w-1/5 sm:w-auto text-center ml-4'}>
            <p className={'font-medium'}>{cron.dayOfWeek}</p>
            <p className={'text-2xs text-neutral-500 uppercase'}>День недели</p>
        </div>
    </div>
);

export default ScheduleCronRow;
