import { httpErrorToHuman } from '@/api/http';
import ServerInstallSvg from '@/assets/images/server_installing.svg';
import ServerRestoreSvg from '@/assets/images/server_restore.svg';
import Can from '@/components/elements/Can';
import ErrorBoundary from '@/components/elements/ErrorBoundary';
import MobileNavigation from '@/components/elements/MobileNavigation';
import ScreenBlock, { NotFound, ServerError } from '@/components/elements/ScreenBlock';
import SidePanel from '@/components/elements/SidePanel';
import Spinner from '@/components/elements/Spinner';
import SubNavigation from '@/components/elements/SubNavigation';
import Suspended from '@/components/elements/Suspended';
import BackupContainer from '@/components/server/backups/BackupContainer';
import ServerConsoleContainer from '@/components/server/console/ServerConsoleContainer';
import DatabasesContainer from '@/components/server/databases/DatabasesContainer';
import EditContainer from '@/components/server/edit/EditContainer';
import ExternalConsole from '@/components/server/ExternalConsole';
import FileEditContainer from '@/components/server/files/FileEditContainer';
import FileManagerContainer from '@/components/server/files/FileManagerContainer';
import InstallListener from '@/components/server/InstallListener';
import NetworkContainer from '@/components/server/network/NetworkContainer';
import PluginContainer from '@/components/server/PluginContainer';
import ScheduleContainer from '@/components/server/schedules/ScheduleContainer';
import ScheduleEditContainer from '@/components/server/schedules/ScheduleEditContainer';
import ServerActivityLogContainer from '@/components/server/ServerActivityLogContainer';
import SettingsContainer from '@/components/server/settings/SettingsContainer';
import StartupContainer from '@/components/server/startup/StartupContainer';
import TransferListener from '@/components/server/TransferListener';
import UsersContainer from '@/components/server/users/UsersContainer';
import WebsocketHandler from '@/components/server/WebsocketHandler';
import RequireServerPermission from '@/hoc/RequireServerPermission';
import useWindowDimensions from '@/plugins/useWindowDimensions';
import { ServerContext } from '@/state/server';
import TransitionRouter from '@/TransitionRouter';
import { useStoreState } from 'easy-peasy';
import React, { useEffect, useState } from 'react';
import * as Icon from 'react-feather';
import { useLocation } from 'react-router';
import { NavLink, Route, Switch, useRouteMatch } from 'react-router-dom';
import { CSSTransition } from 'react-transition-group';
import tw from 'twin.macro';

const ConflictStateRenderer = () => {
    const status = ServerContext.useStoreState((state) => state.server.data?.status || null);
    const isTransferring = ServerContext.useStoreState((state) => state.server.data?.isTransferring || false);

    return status === 'installing' || status === 'install_failed' ? (
        <ScreenBlock
            title={'Запущен установщик'}
            image={ServerInstallSvg}
            message={'Твой сервер скоро будет готов, приходи через пару минут.'}
        />
    ) : status === 'suspended' ? (
        <Suspended />
    ) : (
        <ScreenBlock
            title={isTransferring ? 'Перенос' : 'Восстановление из бэкапа'}
            image={ServerRestoreSvg}
            message={
                isTransferring
                    ? 'Твой сервер переносится на другой узел, приходи через пару минут.'
                    : 'Твой сервер восстанавливается из бэкапа, приходи через пару минут.'
            }
        />
    );
};

export default () => {
    const match = useRouteMatch<{ id: string }>();
    const location = useLocation();
    const { width } = useWindowDimensions();

    const [error, setError] = useState('');
    const rootAdmin = useStoreState((state) => state.user.data!.rootAdmin);
    const databasesEnabled = useStoreState((state) => state.settings.data!.databases);

    const editEnabled = useStoreState((state) => state.storefront.data!.editing.enabled);

    const id = ServerContext.useStoreState((state) => state.server.data?.id);
    const uuid = ServerContext.useStoreState((state) => state.server.data?.uuid);
    const isOwner = ServerContext.useStoreState((state) => state.server.data?.isOwner);
    const serverId = ServerContext.useStoreState((state) => state.server.data?.internalId);
    const getServer = ServerContext.useStoreActions((actions) => actions.server.getServer);
    const eggFeatures = ServerContext.useStoreState((state) => state.server.data?.eggFeatures);
    const inConflictState = ServerContext.useStoreState((state) => state.server.inConflictState);
    const clearServerState = ServerContext.useStoreActions((actions) => actions.clearServerState);
    const databaseLimit = ServerContext.useStoreState((state) => state.server.data?.featureLimits?.databases) || 0;

    useEffect(() => {
        clearServerState();
    }, []);

    useEffect(() => {
        setError('');

        getServer(match.params.id).catch((error) => {
            console.error(error);
            setError(httpErrorToHuman(error));
        });

        return () => {
            clearServerState();
        };
    }, [match.params.id]);

    return (
        <React.Fragment key={'server-router'}>
            {width >= 1280 ? <SidePanel /> : <MobileNavigation />}
            {!uuid || !id ? (
                error ? (
                    <ServerError message={error} />
                ) : (
                    <Spinner size={'large'} centered />
                )
            ) : (
                <>
                    <CSSTransition timeout={150} classNames={'fade'} appear in>
                        <SubNavigation className={'j-down'}>
                            <div>
                                <NavLink to={match.url} exact>
                                    <div css={tw`flex items-center justify-between`}>
                                        Консоль <Icon.Terminal css={tw`ml-1`} size={18} />
                                    </div>
                                </NavLink>
                                <Can action={'file.*'}>
                                    <NavLink to={`${match.url}/files`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Файлы <Icon.Folder css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                <Can action={'backup.*'}>
                                    <NavLink to={`${match.url}/backups`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Бэкапы <Icon.Archive css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                {eggFeatures?.includes('spigot') && (
                                    <Can action={'plugin.*'}>
                                        <NavLink to={`${match.url}/plugins`}>
                                            <div css={tw`flex items-center justify-between`}>
                                                Плагины <Icon.Box css={tw`ml-1`} size={18} />
                                            </div>
                                        </NavLink>
                                    </Can>
                                )}
                                <Can action={'schedule.*'}>
                                    <NavLink to={`${match.url}/schedules`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Планировщик <Icon.Clock css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                {(databasesEnabled && databaseLimit > 0) && (
                                    <Can action={'database.*'}>
                                        <NavLink to={`${match.url}/databases`}>
                                            <div css={tw`flex items-center justify-between`}>
                                                Базы данных <Icon.Database css={tw`ml-1`} size={18} />
                                            </div>
                                        </NavLink>
                                    </Can>
                                )}
                                <Can action={'user.*'}>
                                    <NavLink to={`${match.url}/users`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Пользователи <Icon.Users css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                <Can action={'allocation.*'}>
                                    <NavLink to={`${match.url}/network`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Сеть <Icon.Share2 css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                <Can action={'startup.*'}>
                                    <NavLink to={`${match.url}/startup`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Запуск <Icon.Play css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                <Can action={['settings.*', 'file.sftp']} matchAny>
                                    <NavLink to={`${match.url}/settings`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Настройки <Icon.Settings css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                {editEnabled && isOwner && (
                                    <Can action={['*']}>
                                        <NavLink to={`${match.url}/edit`}>
                                            <div className={'flex items-center justify-between'}>
                                                Характеристики <Icon.Edit className={'ml-1'} size={18} />
                                            </div>
                                        </NavLink>
                                    </Can>
                                )}
                                {/* <NavLink to={`${match.url}/analytics`} exact>
                                    <div css={tw`flex items-center justify-between`}>
                                        Аналитика <Icon.BarChart css={tw`ml-1`} size={18} />
                                    </div>
                                </NavLink> */}
                                <Can action={'activity.*'}>
                                    <NavLink to={`${match.url}/activity`}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Активность <Icon.Eye css={tw`ml-1`} size={18} />
                                        </div>
                                    </NavLink>
                                </Can>
                                {rootAdmin && (
                                    <a href={'/admin/servers/view/' + serverId} rel='noreferrer' target={'_blank'}>
                                        <div css={tw`flex items-center justify-between`}>
                                            Админка <Icon.ExternalLink css={tw`ml-1`} size={18} />
                                        </div>
                                    </a>
                                )}
                            </div>
                        </SubNavigation>
                    </CSSTransition>
                    <InstallListener />
                    <TransferListener />
                    <WebsocketHandler />
                    {inConflictState && (!rootAdmin || (rootAdmin && !location.pathname.endsWith(`/server/${id}`))) ? (
                        <ConflictStateRenderer />
                    ) : (
                        <ErrorBoundary>
                            <TransitionRouter>
                                <Switch location={location}>
                                    <Route path={`${match.path}`} component={ServerConsoleContainer} exact />
                                    <Route path={`${match.path}/console`} component={ExternalConsole} exact />
                                    {/* <Route path={`${match.path}/analytics`} component={AnalyticsContainer} exact /> */}
                                    <Route path={`${match.path}/activity`} exact>
                                        <RequireServerPermission permissions={'activity.*'}>
                                            <ServerActivityLogContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    {eggFeatures?.includes('eula') && (
                                        <Route path={`${match.path}/plugins`} exact>
                                            <RequireServerPermission permissions={'plugin.*'}>
                                                <PluginContainer />
                                            </RequireServerPermission>
                                        </Route>
                                    )}
                                    <Route path={`${match.path}/files`} exact>
                                        <RequireServerPermission permissions={'file.*'}>
                                            <FileManagerContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    <Route path={`${match.path}/files/:action(edit|new)`} exact>
                                        <Spinner.Suspense>
                                            <FileEditContainer />
                                        </Spinner.Suspense>
                                    </Route>
                                    {(databasesEnabled && databaseLimit > 0) && (
                                        <Route path={`${match.path}/databases`} exact>
                                            <RequireServerPermission permissions={'database.*'}>
                                                <DatabasesContainer />
                                            </RequireServerPermission>
                                        </Route>
                                    )}
                                    <Route path={`${match.path}/schedules`} exact>
                                        <RequireServerPermission permissions={'schedule.*'}>
                                            <ScheduleContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    <Route path={`${match.path}/schedules/:id`} exact>
                                        <ScheduleEditContainer />
                                    </Route>
                                    <Route path={`${match.path}/users`} exact>
                                        <RequireServerPermission permissions={'user.*'}>
                                            <UsersContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    <Route path={`${match.path}/backups`} exact>
                                        <RequireServerPermission permissions={'backup.*'}>
                                            <BackupContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    <Route path={`${match.path}/network`} exact>
                                        <RequireServerPermission permissions={'allocation.*'}>
                                            <NetworkContainer />
                                        </RequireServerPermission>
                                    </Route>
                                    <Route path={`${match.path}/startup`} component={StartupContainer} exact />
                                    <Route path={`${match.path}/settings`} component={SettingsContainer} exact />
                                    {editEnabled && (
                                        <Route path={`${match.path}/edit`} component={EditContainer} exact />
                                    )}
                                    <Route path={'*'} component={NotFound} />
                                </Switch>
                            </TransitionRouter>
                        </ErrorBoundary>
                    )}
                </>
            )}
        </React.Fragment>
    );
};
