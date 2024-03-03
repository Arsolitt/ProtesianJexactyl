import React from 'react';
import { useStoreState } from '@/state/hooks';
import { ServerContext } from '@/state/server';
import { history } from '@/components/history';
import StoreRouter from '@/routers/StoreRouter';
import TicketRouter from '@/routers/TicketRouter';
import ServerRouter from '@/routers/ServerRouter';
import Spinner from '@/components/elements/Spinner';
import { Route, Router, Switch } from 'react-router';
import DashboardRouter from '@/routers/DashboardRouter';
import AuthenticationRouter from '@/routers/AuthenticationRouter';
import { NotApproved, NotFound } from '@/components/elements/ScreenBlock';
import AuthenticatedRoute from '@/components/elements/AuthenticatedRoute';
import WelcomeRouter from '@/routers/WelcomeRouter';
import AccountRouter from '@/routers/AccountRouter';

export default () => {
    const authenticated = useStoreState((state) => state.user?.data);
    const approved = useStoreState((state) => state.user.data?.approved);
    const store = useStoreState((state) => state.storefront.data!.enabled);
    const tickets = useStoreState((state) => state.settings.data!.tickets);
    const approvals = useStoreState((state) => state.settings.data!.approvals);

    if (approvals && !approved && authenticated) {
        return (
            <NotApproved
                title={'Ожидание подтверждения'}
                message={'Твой аккаунт ожидает подтверждения администратором.'}
            />
        );
    }

    return (
        <Router history={history}>
            <Switch>
                <Route path={'/auth'}>
                    <Spinner.Suspense>
                        <AuthenticationRouter />
                    </Spinner.Suspense>
                </Route>
                <AuthenticatedRoute path={'/server/:id'}>
                    <Spinner.Suspense>
                        <ServerContext.Provider>
                            <ServerRouter />
                        </ServerContext.Provider>
                    </Spinner.Suspense>
                </AuthenticatedRoute>
                {store && (
                    <AuthenticatedRoute path={'/store'}>
                        <Spinner.Suspense>
                            <StoreRouter />
                        </Spinner.Suspense>
                    </AuthenticatedRoute>
                )}
                {tickets && (
                    <AuthenticatedRoute path={'/tickets'}>
                        <Spinner.Suspense>
                            <TicketRouter />
                        </Spinner.Suspense>
                    </AuthenticatedRoute>
                )}
                <AuthenticatedRoute path={'/account'}>
                    <Spinner.Suspense>
                        <AccountRouter />
                    </Spinner.Suspense>
                </AuthenticatedRoute>
                <AuthenticatedRoute path={'/home'}>
                    <Spinner.Suspense>
                        <DashboardRouter />
                    </Spinner.Suspense>
                </AuthenticatedRoute>
                <Route path={'/'}>
                    <Spinner.Suspense>
                        <WelcomeRouter />
                    </Spinner.Suspense>
                </Route>
                <Route path={'*'} component={NotFound} />
            </Switch>
        </Router>
    );
};
