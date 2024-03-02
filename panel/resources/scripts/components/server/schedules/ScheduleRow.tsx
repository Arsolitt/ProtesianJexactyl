import React from 'react';
import tw from 'twin.macro';
import { format } from 'date-fns';
import * as Icon from 'react-feather';
import { Schedule } from '@/api/server/schedules/getServerSchedules';
import ScheduleCronRow from '@/components/server/schedules/ScheduleCronRow';
import { ru } from 'date-fns/locale';
import Spinner from '@/components/elements/Spinner';

export default ({ schedule }: { schedule: Schedule }) => (
    <>
        <div css={tw`hidden md:block`}>
            <Icon.Calendar />
        </div>
        <div css={tw`flex-1 md:ml-4`}>
            <p>{schedule.name}</p>
            <p css={tw`text-xs text-neutral-400`}>
                Последний запуск:{' '}
                {schedule.lastRunAt ? format(schedule.lastRunAt, "MMM do 'в' h:mma", { locale: ru }) : 'никогда'}
            </p>
        </div>
        <div>
            <p
                css={[
                    tw`py-1 px-3 rounded text-xs uppercase sm:hidden`,
                    schedule.isActive ? tw`bg-main-500 text-main-100` : tw`bg-negative-500 text-negative-100`,
                ]}
            >
                {schedule.isActive ? 'Активна' : 'Выключена'}
            </p>
        </div>
        <ScheduleCronRow cron={schedule.cron} css={tw`mx-auto sm:mx-8 w-full sm:w-auto mt-4 sm:mt-0`} />
        <div>
            <p
                css={[
                    tw`py-1 px-3 rounded text-xs uppercase sm:flex sm:items-center hidden`,
                    schedule.isActive
                        ? !schedule.isProcessing
                            ? tw`bg-main-500 text-main-50`
                            : tw`bg-menuActive-600 text-menuActive-50`
                        : tw`bg-negative-500 text-negative-50`,
                ]}
            >
                {schedule.isProcessing ? (
                    <>
                        <Spinner css={tw`w-3! h-3! mr-2`} />
                        Выполняется
                    </>
                ) : schedule.isActive ? (
                    'Активна'
                ) : (
                    'Выключена'
                )}
            </p>
        </div>
    </>
);
