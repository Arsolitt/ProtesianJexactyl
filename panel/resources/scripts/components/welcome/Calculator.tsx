import React, { ChangeEvent, useState } from 'react';
import classNames from 'classnames';

type Props = {
    className?: string;
};

export default (props: Props) => {
    const [value, setValue] = useState(0);
    const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
        const newValue = parseInt(event.target.value);
        setValue(newValue);
    };

    return (
        <div className={classNames('', props.className)}>
            <input type='range' min={0} max={100} value={value} onChange={handleChange} />
            <span>{value}</span>
        </div>
    );
};
