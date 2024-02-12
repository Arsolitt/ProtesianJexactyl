import http, { FractalResponseData } from '@/api/http';

export interface Resources {
    balance: number;
    slots: number;
    limit: {
        cpu: number;
        ram: number;
        disk: number;
        ports: number;
        backups: number;
        databases: number;
    };
}

export const rawDataToResources = ({ attributes: data }: FractalResponseData): Resources => ({
    balance: data.balance,
    slots: data.slots,
    limit: {
        cpu: data.limit.cpu,
        ram: data.limit.ram,
        disk: data.limit.disk,
        ports: data.limit.ports,
        backups: data.limit.backups,
        databases: data.limit.databases,
    },
});

export const getResources = async (): Promise<Resources> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store')
            .then(({ data }) => resolve(rawDataToResources(data)))
            .catch(reject);
    });
};
