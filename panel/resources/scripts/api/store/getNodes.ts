import http from '@/api/http';

export interface Node {
    id: number;
    name: string;
    location: string;
    total: number;
    used: number;
}

export const rawDataToNode = (data: any): Node => ({
    id: data.id,
    name: data.name,
    location: data.location,
    total: data.total,
    used: data.used,
});

export const getNodes = async (): Promise<Node[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store/nodes')
            .then(({ data }) => resolve((data.data || []).map((d: any) => rawDataToNode(d.attributes))))
            .catch(reject);
    });
};
