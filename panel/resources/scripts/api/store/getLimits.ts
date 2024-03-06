import http, { FractalResponseData } from '@/api/http';

export interface Limits {
    min: {
        memory: number;
        disk: number;
        allocations: number;
        backups: number;
        databases: number;
    };
    max: {
        memory: number;
        disk: number;
        allocations: number;
        backups: number;
        databases: number;
    };
}

export const rawDataToResources = ({ attributes: data }: FractalResponseData): Limits => ({
    min: {
        memory: data.min.memory,
        disk: data.min.disk,
        allocations: data.min.allocations,
        backups: data.min.backups,
        databases: data.min.databases,
    },
    max: {
        memory: data.max.memory,
        disk: data.max.disk,
        allocations: data.max.allocations,
        backups: data.max.backups,
        databases: data.max.databases,
    },
});

export const getLimits = async (): Promise<Limits> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store/limits')
            .then(({ data }) => resolve(rawDataToResources(data)))
            .catch(reject);
    });
};
