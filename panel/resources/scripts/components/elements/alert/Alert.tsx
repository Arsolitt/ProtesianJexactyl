import React from 'react';
import classNames from 'classnames';
import { ExclamationIcon, InformationCircleIcon } from '@heroicons/react/outline';

interface AlertProps {
    type: 'success' | 'info' | 'warning' | 'danger';
    className?: string;
    children: React.ReactNode;
}

export default ({ type, className, children }: AlertProps) => {
    return (
        <div
            className={classNames(
                'flex items-center border-l-8 text-gray-50 rounded-md shadow px-4 py-3',
                {
                    ['border-main-500 bg-main-500/25']: type === 'success',
                    ['border-blue-500 bg-blue-500/25']: type === 'info',
                    ['border-warning-500 bg-warning-500/25']: type === 'warning',
                    ['border-negative-500 bg-negative-500/25']: type === 'danger',
                },
                className
            )}
        >
            {type === 'warning' ? (
                <ExclamationIcon className={'w-6 h-6 text-warning-500 mr-2'} />
            ) : type === 'success' ? (
                <InformationCircleIcon className={'w-6 h-6 text-main-500 mr-2'} />
            ) : type === 'info' ? (
                <InformationCircleIcon className={'w-6 h-6 text-blue-500 mr-2'} />
            ) : (
                <InformationCircleIcon className={'w-6 h-6 text-negative-500 mr-2'} />
            )}
            {children}
        </div>
    );
};
