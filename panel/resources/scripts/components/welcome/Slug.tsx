import { Button } from '@/components/elements/button';
import { useStoreState } from 'easy-peasy';
import React from 'react';
import { Link } from 'react-router-dom';

interface Props {
    className?: string;
}
export default (props: Props) => {
    const user = useStoreState((state) => state.user.data!);

    return (
        <div className={'w-full h-[calc(100vh)] flex justify-center items-center flex-col' + ' ' + props.className}>
            <p className={'flex flex-col items-center'}>
                <span
                    className={
                        'font-bold text-center text-[calc(12vw)] lg:text-9xl bg-gradient-to-l from-main-400 to-main-600 bg-clip-text text-transparent'
                    }
                >
                    ProtesiaN Host
                </span>
            </p>
            {(!user && (
                <Link to={'/auth/login'}>
                    <Button.Success size={Button.Sizes.Large}>Вход в систему</Button.Success>
                </Link>
            )) || (
                <Link to={'/home'}>
                    <Button.Danger size={Button.Sizes.Large}>Профиль {user.username}</Button.Danger>
                </Link>
            )}
            <span className={'font-bold text-sm text-gray-400 text-center'}>
                Не бейте админа, он не успел закончить лендинг к релизу {'>_<'}
            </span>
        </div>
    );
};
