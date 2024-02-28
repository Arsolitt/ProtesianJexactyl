import http from '@/api/http';

interface Params {
    name: string;
    description: string | null;

    memory: number;
    disk: number;
    allocations: number;
    backups: number;
    databases: number;

    egg: number;
    nest: number;
}

interface Data {
    success: boolean;
    id: string;
}

export default (params: Params, egg: number, nest: number): Promise<Data> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/store/create', { ...params, egg, nest })
            .then((data) => resolve(data.data))
            .catch(reject);
    });
};
