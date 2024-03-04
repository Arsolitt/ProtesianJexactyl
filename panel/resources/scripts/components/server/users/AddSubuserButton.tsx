import React, { useState } from 'react';
import { Button } from '@/components/elements/button/index';
import EditSubuserModal from '@/components/server/users/EditSubuserModal';

export default () => {
    const [visible, setVisible] = useState(false);

    return (
        <>
            <EditSubuserModal visible={visible} onModalDismissed={() => setVisible(false)} />
            <Button.Success onClick={() => setVisible(true)}>Добавить</Button.Success>
        </>
    );
};
