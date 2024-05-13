import http from '@/api/http';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import ProgressBar from '@/components/elements/ProgressBar';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import { useStoreState } from 'easy-peasy';
import React from 'react';
import * as Icon from 'react-feather';
import { NavLink } from 'react-router-dom';
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
            <a href='/' className='flex justify-center items-center '>
                <img
                    className={'p-2 w-17 h-17'}
                    src={logo ?? 'https://avatars.githubusercontent.com/u/91636558'}
                    alt={'logo'}
                />
            </a>
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
                                    {credits.toFixed(2)} ₽
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
                <a
                    href={'https://discord.gg/PTYfmZCqhK'}
                    target={'_blank'}
                    className={'navigation-link'}
                    rel='noreferrer'
                >
                    <Tooltip placement={'bottom'} content={'Дискорд'}>
                        <div className={'bg-gray-700 rounded-lg p-2 my-8'}>
                            <svg
                                viewBox='0 -28.5 256 256'
                                version='1.1'
                                xmlns='http://www.w3.org/2000/svg'
                                xmlnsXlink='http://www.w3.org/1999/xlink'
                                preserveAspectRatio='xMidYMid'
                                fill='#ffffff'
                            >
                                <g id='SVGRepo_bgCarrier' strokeWidth='0'></g>
                                <g id='SVGRepo_tracerCarrier' strokeLinecap='round' strokeLinejoin='round'></g>
                                <g id='SVGRepo_iconCarrier'>
                                    <g>
                                        <path
                                            d='M216.856339,16.5966031 C200.285002,8.84328665 182.566144,3.2084988 164.041564,0 C161.766523,4.11318106 159.108624,9.64549908 157.276099,14.0464379 C137.583995,11.0849896 118.072967,11.0849896 98.7430163,14.0464379 C96.9108417,9.64549908 94.1925838,4.11318106 91.8971895,0 C73.3526068,3.2084988 55.6133949,8.86399117 39.0420583,16.6376612 C5.61752293,67.146514 -3.4433191,116.400813 1.08711069,164.955721 C23.2560196,181.510915 44.7403634,191.567697 65.8621325,198.148576 C71.0772151,190.971126 75.7283628,183.341335 79.7352139,175.300261 C72.104019,172.400575 64.7949724,168.822202 57.8887866,164.667963 C59.7209612,163.310589 61.5131304,161.891452 63.2445898,160.431257 C105.36741,180.133187 151.134928,180.133187 192.754523,160.431257 C194.506336,161.891452 196.298154,163.310589 198.110326,164.667963 C191.183787,168.842556 183.854737,172.420929 176.223542,175.320965 C180.230393,183.341335 184.861538,190.991831 190.096624,198.16893 C211.238746,191.588051 232.743023,181.531619 254.911949,164.955721 C260.227747,108.668201 245.831087,59.8662432 216.856339,16.5966031 Z M85.4738752,135.09489 C72.8290281,135.09489 62.4592217,123.290155 62.4592217,108.914901 C62.4592217,94.5396472 72.607595,82.7145587 85.4738752,82.7145587 C98.3405064,82.7145587 108.709962,94.5189427 108.488529,108.914901 C108.508531,123.290155 98.3405064,135.09489 85.4738752,135.09489 Z M170.525237,135.09489 C157.88039,135.09489 147.510584,123.290155 147.510584,108.914901 C147.510584,94.5396472 157.658606,82.7145587 170.525237,82.7145587 C183.391518,82.7145587 193.761324,94.5189427 193.539891,108.914901 C193.539891,123.290155 183.391518,135.09489 170.525237,135.09489 Z'
                                            fill='#ffffff'
                                            fillRule='nonzero'
                                        ></path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </Tooltip>
                </a>
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
