import React from 'react';
import { useHistory, useLocation } from 'react-router';
import { NotFound } from '@/components/elements/ScreenBlock';
import { Route, Switch, useRouteMatch } from 'react-router-dom';
import WelcomeContainer from '@/components/welcome/WelcomeContainer';

export default () => {
    const history = useHistory();
    const location = useLocation();
    const { path } = useRouteMatch();

    return (
        <div className={'pt-8 xl:pt-32'}>
            <Switch location={location}>
                <Route path={`/`} component={WelcomeContainer} exact />
                <Route path={'*'}>
                    <NotFound onBack={() => history.push('/')} />
                </Route>
            </Switch>
        </div>
    );
};
