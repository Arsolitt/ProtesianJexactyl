import http from '@/api/http';
import { PurchaseValues } from '@/components/store/forms/PurchaseForm';

interface Data {
    success: boolean;
    url: string;
}

export default (values: PurchaseValues): Promise<Data> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/store/payment', { ...values })
            .then((data) => resolve(data.data))
            .catch(reject);
    });
};
