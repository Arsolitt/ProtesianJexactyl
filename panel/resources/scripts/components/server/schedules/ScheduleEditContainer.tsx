import tw from 'twin.macro';
import { format } from 'date-fns';
import isEqual from 'react-fast-compare';
import useFlash from '@/plugins/useFlash';
import Can from '@/components/elements/Can';
import { ServerContext } from '@/state/server';
import Spinner from '@/components/elements/Spinner';
import { useHistory, useParams } from 'react-router-dom';
import { Button } from '@/components/elements/button/index';
import React, { useCallback, useEffect, useState } from 'react';
import FlashMessageRender from '@/components/FlashMessageRender';
import PageContentBlock from '@/components/elements/PageContentBlock';
import NewTaskButton from '@/components/server/schedules/NewTaskButton';
import getServerSchedule from '@/api/server/schedules/getServerSchedule';
import ScheduleTaskRow from '@/components/server/schedules/ScheduleTaskRow';
import ScheduleCronRow from '@/components/server/schedules/ScheduleCronRow';
import EditScheduleModal from '@/components/server/schedules/EditScheduleModal';
import RunScheduleButton from '@/components/server/schedules/RunScheduleButton';
import DeleteScheduleButton from '@/components/server/schedules/DeleteScheduleButton';
import { ru } from 'date-fns/locale';

interface Params {
    id: string;
}

const CronBox = ({ title, value }: { title: string; value: string }) => (
    <div css={tw`bg-inert-700 rounded p-3`}>
        <p css={tw`text-inert-200 text-sm`}>{title}</p>
        <p css={tw`text-xl font-medium text-inert-50`}>{value}</p>
    </div>
);

const ActivePill = ({ active }: { active: boolean }) => (
    <span
        css={[
            tw`rounded-full px-2 py-px text-xs ml-4 uppercase`,
            active ? tw`bg-main-500 text-main-100` : tw`bg-negative-500 text-negative-100`,
        ]}
    >
        {active ? 'Активна' : 'Выключена'}
    </span>
);

export default () => {
    const history = useHistory();
    const { id: scheduleId } = useParams<Params>();

    const id = ServerContext.useStoreState((state) => state.server.data!.id);
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const [isLoading, setIsLoading] = useState(true);
    const [showEditModal, setShowEditModal] = useState(false);

    const schedule = ServerContext.useStoreState(
        (st) => st.schedules.data.find((s) => s.id === Number(scheduleId)),
        isEqual
    );
    const appendSchedule = ServerContext.useStoreActions((actions) => actions.schedules.appendSchedule);

    useEffect(() => {
        if (schedule?.id === Number(scheduleId)) {
            setIsLoading(false);
            return;
        }

        clearFlashes('schedules');
        getServerSchedule(uuid, Number(scheduleId))
            .then((schedule) => appendSchedule(schedule))
            .catch((error) => {
                console.error(error);
                clearAndAddHttpError({ error, key: 'schedules' });
            })
            .then(() => setIsLoading(false));
    }, [scheduleId]);

    const toggleEditModal = useCallback(() => {
        setShowEditModal((s) => !s);
    }, []);

    return (
        <PageContentBlock title={'Schedules'}>
            <FlashMessageRender byKey={'schedules'} css={tw`mb-4`} />
            {!schedule || isLoading ? (
                <Spinner size={'large'} centered />
            ) : (
                <>
                    <ScheduleCronRow cron={schedule.cron} css={tw`sm:hidden bg-inert-700 rounded mb-4 p-3`} />
                    <div css={tw`rounded shadow`}>
                        <div
                            css={tw`sm:flex items-center bg-inert-900 p-3 sm:p-6 border-b-4 border-inert-600 rounded-t`}
                        >
                            <div css={tw`flex-1`}>
                                <h3 css={tw`flex items-center text-inert-100 text-2xl`}>
                                    {schedule.name}
                                    {schedule.isProcessing ? (
                                        <span
                                            css={tw`flex items-center rounded-full px-2 py-px text-xs ml-4 uppercase bg-menuActive-600 text-menuActive-50`}
                                        >
                                            <Spinner css={tw`w-3! h-3! mr-2`} />
                                            Выполняется
                                        </span>
                                    ) : (
                                        <ActivePill active={schedule.isActive} />
                                    )}
                                </h3>
                                <p css={tw`mt-1 text-sm text-inert-200`}>
                                    Последний запуск:&nbsp;
                                    {schedule.lastRunAt ? (
                                        format(schedule.lastRunAt, "MMM do 'at' h:mma")
                                    ) : (
                                        <span css={tw`text-inert-300`}>никогда</span>
                                    )}
                                    <span css={tw`ml-4 pl-4 border-l-4 border-inert-600 py-px`}>
                                        Следующий запуск:&nbsp;
                                        {schedule.nextRunAt ? (
                                            format(schedule.nextRunAt, "MMM do 'at' h:mma", { locale: ru })
                                        ) : (
                                            <span css={tw`text-inert-300`}>никогда</span>
                                        )}
                                    </span>
                                </p>
                            </div>
                            <div css={tw`flex sm:block mt-3 sm:mt-0`}>
                                <Can action={'schedule.update'}>
                                    <Button.Text className={'flex-1 mr-4'} onClick={toggleEditModal}>
                                        Редактировать
                                    </Button.Text>
                                    <NewTaskButton schedule={schedule} />
                                </Can>
                            </div>
                        </div>
                        <div css={tw`hidden sm:grid grid-cols-5 md:grid-cols-5 gap-4 mb-4 mt-4`}>
                            <CronBox title={'Минуты'} value={schedule.cron.minute} />
                            <CronBox title={'Часы'} value={schedule.cron.hour} />
                            <CronBox title={'День месяца'} value={schedule.cron.dayOfMonth} />
                            <CronBox title={'Месяц'} value={schedule.cron.month} />
                            <CronBox title={'День недели'} value={schedule.cron.dayOfWeek} />
                        </div>
                        <div css={tw`bg-inert-700 rounded-b`}>
                            {schedule.tasks.length > 0
                                ? schedule.tasks
                                      .sort((a, b) =>
                                          a.sequenceId === b.sequenceId ? 0 : a.sequenceId > b.sequenceId ? 1 : -1
                                      )
                                      .map((task) => (
                                          <ScheduleTaskRow
                                              key={`${schedule.id}_${task.id}`}
                                              task={task}
                                              schedule={schedule}
                                          />
                                      ))
                                : null}
                        </div>
                    </div>
                    <EditScheduleModal visible={showEditModal} schedule={schedule} onModalDismissed={toggleEditModal} />
                    <div css={tw`mt-6 flex sm:justify-end`}>
                        <Can action={'schedule.delete'}>
                            <DeleteScheduleButton
                                scheduleId={schedule.id}
                                onDeleted={() => history.push(`/server/${id}/schedules`)}
                            />
                        </Can>
                        {schedule.tasks.length > 0 && (
                            <Can action={'schedule.update'}>
                                <RunScheduleButton schedule={schedule} />
                            </Can>
                        )}
                    </div>
                </>
            )}
        </PageContentBlock>
    );
};
