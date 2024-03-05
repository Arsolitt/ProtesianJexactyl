import React from 'react';
import classNames from 'classnames';

type Props = {
    className?: string;
};

export default (props: Props) => {
    return <div className={classNames('', props.className)}></div>;
};
