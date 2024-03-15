import http from '@/api/http';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import ProgressBar from '@/components/elements/ProgressBar';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import { useStoreState } from 'easy-peasy';
import React from 'react';
import * as Icon from 'react-feather';
import { Link, NavLink } from 'react-router-dom';
import styled from 'styled-components/macro';
import tw from 'twin.macro';

export default () => {
    const logo = useStoreState((state) => state.settings.data?.logo);
    const tickets = useStoreState((state) => state.settings.data!.tickets);
    const store = useStoreState((state) => state.storefront.data!.enabled);
    const rootAdmin = useStoreState((state) => state.user.data!.rootAdmin);
    const credits = useStoreState((state) => state.user.data!.credits);

    const onTriggerLogout = () => {
        http.post('/auth/logout').finally(() => {
            // @ts-expect-error this is valid
            window.location = '/';
        });
    };

    const PanelDiv = styled.div`
        ${tw`h-screen sticky bg-neutral-800 flex flex-col w-20 fixed top-0`};

        & > div {
            ${tw`mx-auto`};

            & > a,
            & > div {
                &:hover {
                    ${tw`text-menuActive-300`};
                }

                &:active,
                &.active {
                    ${tw`text-menuActive-500`};
                }
            }
        }
    `;

    return (
        <PanelDiv>
            <ProgressBar />
            <Link to={'/'} className='flex justify-center items-center '>
                <img
                    className={'p-2 w-17 h-17'}
                    src={logo ?? 'https://avatars.githubusercontent.com/u/91636558'}
                    alt={'logo'}
                />
            </Link>
            <div>
                <div className={'navigation-link relative z-30'}>
                    <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                        <SearchContainer size={32} />
                    </div>
                </div>
                <NavLink to={'/home'} className={'navigation-link relative z-20'} exact>
                    <Tooltip placement={'bottom'} content={'Серверы'}>
                        <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                            <Icon.Server size={32} />
                        </div>
                    </Tooltip>
                </NavLink>
                <NavLink to={'/account'} className={'navigation-link relative z-10'}>
                    <Tooltip placement={'bottom'} content={'Профиль'}>
                        <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                            <Icon.User size={32} />
                        </div>
                    </Tooltip>
                </NavLink>
                {store && (
                    <NavLink to={'/store/credits'} className={'navigation-link'}>
                        <Tooltip placement={'bottom'} content={'Финансы'}>
                            <div className={'bg-gray-700 rounded-lg p-2 my-8 relative'}>
                                <Icon.ShoppingCart size={32} />
                                <span
                                    className={
                                        'absolute -bottom-4 left-1/2 transform -translate-x-1/2 text-main-500 whitespace-nowrap bg-gray-700 rounded-lg px-1'
                                    }
                                >
                                    {credits} ₽
                                </span>
                            </div>
                        </Tooltip>
                    </NavLink>
                )}
                {tickets && (
                    <NavLink to={'/tickets'} className={'navigation-link'}>
                        <Tooltip placement={'bottom'} content={'Поддержка'}>
                            <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                                <Icon.HelpCircle size={32} />
                            </div>
                        </Tooltip>
                    </NavLink>
                )}
                {rootAdmin && (
                    <a href={'/admin'} className={'navigation-link'}>
                        <Tooltip placement={'bottom'} content={'Админка'}>
                            <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                                <Icon.Settings size={32} />
                            </div>
                        </Tooltip>
                    </a>
                )}
                <div id={'logo'}>
                    <button onClick={onTriggerLogout} className={'navigation-link'}>
                        <Tooltip placement={'bottom'} content={'Выход'}>
                            <div className={'flex flex-row fixed bottom-0 mb-8 bg-gray-700 rounded-lg p-2'}>
                                <Icon.LogOut size={32} />
                            </div>
                        </Tooltip>
                    </button>
                </div>
            </div>
        </PanelDiv>
    );
};
