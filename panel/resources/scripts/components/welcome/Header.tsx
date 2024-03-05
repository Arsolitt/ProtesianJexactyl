import React from 'react';
import { Link } from 'react-router-dom';
import { useStoreState } from 'easy-peasy';
import { Button } from '@/components/elements/button';

interface Props {
    className?: string;
}
export default (props: Props) => {
    const user = useStoreState((state) => state.user.data!);
    const logo = useStoreState((state) => state.settings.data?.logo);

    return (
        <div className={'w-full flex justify-between items-center' + ' ' + props.className}>
            <Link to={'/'} className={'inline-flex items-center'}>
                <img
                    className={'w-16 h-16'}
                    src={logo ?? 'https://avatars.githubusercontent.com/u/91636558'}
                    alt={'logo'}
                />
                <span className={'font-bold text-4xl text-main-400 ml-5 hidden md:inline'}>ProtesiaN</span>
            </Link>
            {(!user && (
                <Link to={'/auth/login'}>
                    <Button.Success size={Button.Sizes.Large} className='Success'>
                        Вход
                    </Button.Success>
                </Link>
            )) || (
                <Link to={'/home'}>
                    <Button.Text size={Button.Sizes.Large} className='Success'>
                        {user.username}
                    </Button.Text>
                </Link>
            )}
        </div>
    );
};
