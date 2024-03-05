import React from 'react';
// import { useStoreState } from 'easy-peasy';
import Header from '@/components/welcome/Header';
import Description from '@/components/welcome/Description';
import Calculator from '@/components/welcome/Calculator';
import Footer from '@/components/welcome/Footer';

// const user = useStoreState((state) => state.user.data!);

export default () => {
    return (
        <>
            <Header className={'h-20'} />
            <div className='content md:h-[calc(100vh-10rem)] flex flex-col md:flex-row'>
                <Description className={'w-full md:w-1/2 h-[calc(100vh-5rem)] md:h-full'} />
                <Calculator className={'w-full md:w-1/2 h-[calc(100vh)] md:h-full'} />
            </div>
            <Footer className={'h-20 bg-warning-500'} />
        </>
    );
};
