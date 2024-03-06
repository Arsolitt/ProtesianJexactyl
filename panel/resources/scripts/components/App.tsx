import React from 'react';
import tw from 'twin.macro';
import '@/assets/tailwind.css';
import { store } from '@/state';
import { StoreProvider } from 'easy-peasy';
import { hot } from 'react-hot-loader/root';
import { history } from '@/components/history';
import { SiteSettings } from '@/state/settings';
import IndexRouter from '@/routers/IndexRouter';
import { setupInterceptors } from '@/api/interceptors';
import { StorefrontSettings } from '@/state/storefront';
import GlobalStylesheet from '@/assets/css/GlobalStylesheet';

interface ExtendedWindow extends Window {
    SiteConfiguration?: SiteSettings;
    StoreConfiguration?: StorefrontSettings;
    JexactylUser?: {
        uuid: string;
        username: string;
        email: string;
        approved: boolean;
        verified: boolean;
        /* eslint-disable camelcase */
        discord_id: string;
        root_admin: boolean;
        use_totp: boolean;
        referral_code: string;
        language: string;
        created_at: string;
        updated_at: string;
        discount: number;
        credits: number;
        reward: number;
        /* eslint-enable camelcase */
    };
}

setupInterceptors(history);

const App = () => {
    const { JexactylUser, SiteConfiguration, StoreConfiguration } = window as ExtendedWindow;

    if (JexactylUser && !store.getState().user.data) {
        store.getActions().user.setUserData({
            uuid: JexactylUser.uuid,
            username: JexactylUser.username,
            email: JexactylUser.email,
            approved: JexactylUser.approved,
            verified: JexactylUser.verified,
            discordId: JexactylUser.discord_id,
            language: JexactylUser.language,
            rootAdmin: JexactylUser.root_admin,
            useTotp: JexactylUser.use_totp,
            referralCode: JexactylUser.referral_code,
            createdAt: new Date(JexactylUser.created_at),
            updatedAt: new Date(JexactylUser.updated_at),
            discount: JexactylUser.discount,
            credits: JexactylUser.credits,
            reward: JexactylUser.reward,
        });
    }

    if (!store.getState().settings.data) {
        store.getActions().settings.setSettings(SiteConfiguration!);
    }

    if (!store.getState().storefront.data) {
        store.getActions().storefront.setStorefront(StoreConfiguration!);
    }

    return (
        <>
            <GlobalStylesheet />
            <StoreProvider store={store}>
                <div css={tw`mx-auto w-auto`}>
                    <IndexRouter />
                </div>
            </StoreProvider>
        </>
    );
};

export default hot(App);
