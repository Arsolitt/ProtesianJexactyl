import http, { FractalResponseData } from '@/api/http';

export interface Costs {
    memory: number;
    disk: number;
    slots: number;
    allocations: number;
    backups: number;
    databases: number;
}

export const rawDataToCosts = ({ attributes: data }: FractalResponseData): Costs => ({
    memory: data.memory,
    disk: data.disk,
    slots: data.slots,
    allocations: data.allocations,
    backups: data.backups,
    databases: data.databases,
});

export const getCosts = async (): Promise<Costs> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store/costs')
            .then(({ data }) => resolve(rawDataToCosts(data)))
            .catch(reject);
    });
};
