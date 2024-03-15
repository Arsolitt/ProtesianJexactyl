import http from '@/api/http';

export interface ReferralActivity {
    code: string;
    userId: number;
    userEmail: string;
    totalPayments: number;
    createdAt: Date | null;
}

export const rawDataToReferralActivity = (data: any): ReferralActivity => ({
    code: data.code,
    userId: data.user_id,
    userEmail: data.username,
    totalPayments: data.total_payments,
    createdAt: data.created_at ? new Date(data.created_at) : null,
});

export default (): Promise<ReferralActivity[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/account/referrals/activity')
            .then(({ data }) => resolve((data.data || []).map((d: any) => rawDataToReferralActivity(d.attributes))))
            .catch(reject);
    });
};
