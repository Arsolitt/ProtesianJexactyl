import http from '@/api/http';

export interface Resources {
    memory: number;
    disk: number;
    allocations: number;
    backups: number;
    databases: number;
}

export default (uuid: string, resources: Resources): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/servers/${uuid}/edit`, {
            resources,
        })
            .then(() => resolve())
            .catch(reject);
    });
};
