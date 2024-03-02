import { Line } from 'react-chartjs-2';
import { ServerContext } from '@/state/server';
import { bytesToString } from '@/lib/formatters';
import React, { useEffect, useRef } from 'react';
import { SocketEvent } from '@/components/server/events';
import useWebsocketEvent from '@/plugins/useWebsocketEvent';
import ChartBlock from '@/components/server/console/ChartBlock';
import { useChart, useChartTickLabel } from '@/components/server/console/chart';

export default () => {
    const status = ServerContext.useStoreState((state) => state.status.value);
    const limits = ServerContext.useStoreState((state) => state.server.data!.limits);
    const previous = useRef<Record<'tx' | 'rx', number>>({ tx: -1, rx: -1 });

    const cpu = useChartTickLabel('Процессор', limits.cpu, '%', 2);
    const disk = useChartTickLabel('Диск', limits.disk, 'МБ');
    const memory = useChartTickLabel('Память', limits.memory, 'МБ');
    const networkIn = useChart('Входящий трафик', {
        sets: 1,
        options: {
            scales: {
                y: {
                    ticks: {
                        callback(value) {
                            return bytesToString(typeof value === 'string' ? parseInt(value, 10) : value);
                        },
                    },
                },
            },
        },
        callback(opts) {
            return {
                ...opts,
                label: 'Входящий трафик',
            };
        },
    });
    const networkOut = useChart('Исходящий трафик', {
        sets: 1,
        options: {
            scales: {
                y: {
                    ticks: {
                        callback(value) {
                            return bytesToString(typeof value === 'string' ? parseInt(value, 10) : value);
                        },
                    },
                },
            },
        },
        callback(opts) {
            return {
                ...opts,
                label: 'Исходящий трафик',
            };
        },
    });

    useEffect(() => {
        if (status === 'offline') {
            cpu.clear();
            disk.clear();
            memory.clear();
            networkIn.clear();
            networkOut.clear();
        }
    }, [status]);

    useWebsocketEvent(SocketEvent.STATS, (data: string) => {
        let values: any = {};
        try {
            values = JSON.parse(data);
        } catch (e) {
            return;
        }

        cpu.push(values.cpu_absolute);
        disk.push(Math.floor(values.disk_bytes / 1024 / 1024));
        memory.push(Math.floor(values.memory_bytes / 1024 / 1024));
        networkIn.push(previous.current.tx < 0 ? 0 : Math.max(0, values.network.tx_bytes - previous.current.tx));
        networkOut.push(previous.current.rx < 0 ? 0 : Math.max(0, values.network.rx_bytes - previous.current.rx));

        previous.current = { tx: values.network.tx_bytes, rx: values.network.rx_bytes };
    });

    return (
        <>
            <ChartBlock title={'Загрузка процессора'}>
                <Line {...cpu.props} />
            </ChartBlock>
            <ChartBlock title={'Диск'}>
                <Line {...disk.props} />
            </ChartBlock>
            <ChartBlock title={'Оперативная память'}>
                <Line {...memory.props} />
            </ChartBlock>
            <ChartBlock title={'Входящий трафик'}>
                <Line {...networkIn.props} />
            </ChartBlock>
            <ChartBlock title={'Исходящий трафик'}>
                <Line {...networkOut.props} />
            </ChartBlock>
        </>
    );
};
