import React from 'react';
import { useLocation } from 'react-router';
import TransitionRouter from '@/TransitionRouter';
import Spinner from '@/components/elements/Spinner';
import SidePanel from '@/components/elements/SidePanel';
import { Route, Switch } from 'react-router-dom';
import { NotFound } from '@/components/elements/ScreenBlock';
import SubNavigation from '@/components/elements/SubNavigation';
import useWindowDimensions from '@/plugins/useWindowDimensions';
import MobileNavigation from '@/components/elements/MobileNavigation';
import DashboardContainer from '@/components/dashboard/DashboardContainer';
import InformationContainer from '@/components/elements/InformationContainer';

export default () => {
    const location = useLocation();
    const { width } = useWindowDimensions();

    return (
        <>
            {width >= 1280 ? <SidePanel /> : <MobileNavigation />}
            <SubNavigation className={'lg:visible invisible'}>
                <div>
                    <InformationContainer />
                </div>
            </SubNavigation>
            <TransitionRouter>
                <React.Suspense fallback={<Spinner centered />}>
                    <Switch location={location}>
                        <Route path={'/home'} exact>
                            <DashboardContainer />
                        </Route>
                        <Route path={'*'}>
                            <NotFound />
                        </Route>
                    </Switch>
                </React.Suspense>
            </TransitionRouter>
        </>
    );
};
