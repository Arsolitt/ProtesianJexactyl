import React from 'react';
import { useLocation } from 'react-router';
import { NotFound } from '@/components/elements/ScreenBlock';
import { Route, Switch } from 'react-router-dom';
import WelcomeContainer from '@/components/welcome/WelcomeContainer';

export default () => {
    const location = useLocation();

    return (
        <div className={'pt-8 xl:pt-32'}>
            <Switch location={location}>
                <Route path={`/`} component={WelcomeContainer} exact />
                <Route path={'*'}>
                    <NotFound />
                </Route>
            </Switch>
        </div>
    );
};
