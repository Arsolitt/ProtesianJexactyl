import React from 'react';
import tw from 'twin.macro';

export default () => {
    return (
        <>
            <div css={tw`md:w-1/2 h-full bg-inert-600`}>
                <div css={tw`flex flex-col`}>
                    <h2 css={tw`py-4 px-6 font-bold`}>Примеры</h2>
                    <div css={tw`flex py-4 px-6 bg-inert-500`}>
                        <div css={tw`w-1/2`}>*/5 * * * *</div>
                        <div css={tw`w-1/2`}>Каждые 5 минут</div>
                    </div>
                    <div css={tw`flex py-4 px-6`}>
                        <div css={tw`w-1/2`}>0 */1 * * *</div>
                        <div css={tw`w-1/2`}>Каждый час</div>
                    </div>
                    <div css={tw`flex py-4 px-6 bg-inert-500`}>
                        <div css={tw`w-1/2`}>0 8-12 * * *</div>
                        <div css={tw`w-1/2`}>Диапазон часов</div>
                    </div>
                    <div css={tw`flex py-4 px-6`}>
                        <div css={tw`w-1/2`}>0 0 * * *</div>
                        <div css={tw`w-1/2`}>Раз в день</div>
                    </div>
                    <div css={tw`flex py-4 px-6 bg-inert-500`}>
                        <div css={tw`w-1/2`}>0 0 * * MON</div>
                        <div css={tw`w-1/2`}>Каждый понедельник</div>
                    </div>
                </div>
            </div>
            <div css={tw`md:w-1/2 h-full bg-inert-600`}>
                <h2 css={tw`py-4 px-6 font-bold`}>Специальные символы</h2>
                <div css={tw`flex flex-col`}>
                    <div css={tw`flex py-4 px-6 bg-inert-500`}>
                        <div css={tw`w-1/2`}>*</div>
                        <div css={tw`w-1/2`}>Любое значение</div>
                    </div>
                    <div css={tw`flex py-4 px-6`}>
                        <div css={tw`w-1/2`}>,</div>
                        <div css={tw`w-1/2`}>Разделитель значений</div>
                    </div>
                    <div css={tw`flex py-4 px-6 bg-inert-500`}>
                        <div css={tw`w-1/2`}>-</div>
                        <div css={tw`w-1/2`}>Диапазон значений</div>
                    </div>
                    <div css={tw`flex py-4 px-6`}>
                        <div css={tw`w-1/2`}>/</div>
                        <div css={tw`w-1/2`}>Значение шагов</div>
                    </div>
                </div>
            </div>
        </>
    );
};
