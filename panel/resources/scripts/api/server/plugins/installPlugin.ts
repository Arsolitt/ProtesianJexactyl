import http from '@/api/http';

export default async (uuid: string, id: number, name: string): Promise<void> => {
    const { data } = await http.post(`/api/client/servers/${uuid}/plugins/install/${id}`, { filename: name + '.jar' });

    return data.data || [];
};
