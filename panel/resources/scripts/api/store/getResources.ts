import http, { FractalResponseData } from '@/api/http';

export interface Resources {
    balance: number;
    slots: number;
    cpu: number;
    ram: number;
    disk: number;
    port: number;
    backup: number;
    database: number;
}

export const rawDataToResources = ({ attributes: data }: FractalResponseData): Resources => ({
    balance: data.balance,
    slots: data.slots,
    cpu: data.cpu,
    ram: data.ram,
    disk: data.disk,
    port: data.port,
    backup: data.backup,
    database: data.database,
});

export const getResources = async (): Promise<Resources> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store')
            .then(({ data }) => resolve(rawDataToResources(data)))
            .catch(reject);
    });
};
