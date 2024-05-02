import http from '@/api/http';

export interface ReferralStats {
    totalRevenue: number;
    totalReferrals: number;
}

export const rawDataToReferralStats = (data: any): ReferralStats => ({
    totalRevenue: data.total_revenue,
    totalReferrals: data.total_referrals,
});

export default (): Promise<ReferralStats> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/account/referrals/stats')
            .then(({ data }) => resolve(rawDataToReferralStats(data)))
            .catch(reject);
    });
};
