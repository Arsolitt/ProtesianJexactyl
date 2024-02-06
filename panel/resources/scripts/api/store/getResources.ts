import http, { FractalResponseData } from '@/api/http';

export interface Resources {
    balance: number;
    slots: number;
}

export const rawDataToResources = ({ attributes: data }: FractalResponseData): Resources => ({
    balance: data.balance,
    slots: data.slots,
});

export const getResources = async (): Promise<Resources> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/store')
            .then(({ data }) => resolve(rawDataToResources(data)))
            .catch(reject);
    });
};
