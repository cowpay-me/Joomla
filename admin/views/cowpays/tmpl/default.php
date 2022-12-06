<?php

/**

 * @package     Joomla.Administrator

 * @subpackage  com_cowpay

 *

 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.

 * @license     GNU General Public License version 2 or later; see LICENSE.txt

 */



// No direct access to this file

defined('_JEXEC') or die('Restricted Access');

?>

<form action="index.php?option=com_cowpay&view=cowpays" method="post" id="adminForm" name="adminForm">

    <table class="table table-striped table-hover">

        <thead>

        <tr>

            <th width="1%"><?php echo JText::_('COM_COWPAY_NUM'); ?></th>

            <th width="2%">

                <?php echo JHtml::_('grid.checkall'); ?>

            </th>

            <th width="30%">

                <?php echo JText::_('COM_COWPAY_NAME') ;?>

            </th>

            <th width="10%">

                <?php echo JText::_('COM_COWPAY_PHONE'); ?>

            </th>

            <th width="15%">

                <?php echo JText::_('COM_COWPAY_EMAIL'); ?>

            </th>

            <th width="2%">

                <?php echo JText::_('COM_COWPAY_AMOUNT'); ?>

            </th>

            <th width="3%">

                <?php echo JText::_('COM_COWPAY_METHOD'); ?>

            </th>

            <th width="5%">

                <?php echo JText::_('COM_COWPAY_STATUS'); ?>

            </th>


            <th width="5%">

                <?php echo JText::_('COM_COWPAY_REFERENCE_ID') ;?>

            </th>

            <th width="5%">

                <?php echo JText::_('COM_COWPAY_CREATED_AT') ;?>

            </th>


        </tr>

        </thead>

        <tfoot>

        <tr>

            <td colspan="5">

                <?php echo $this->pagination->getListFooter(); ?>

            </td>

        </tr>

        </tfoot>

        <tbody>

        <?php if (!empty($this->items)) : ?>

            <?php foreach ($this->items as $i => $row) :

                $link = JRoute::_('index.php?option=com_cowpay&task=cowpay.edit&id=' . $row->id);

                ?>

                <tr>

                    <td><?php echo $this->pagination->getRowOffset($i); ?></td>

                    <td>

                        <?php echo JHtml::_('grid.id', $i, $row->id); ?>

                    </td>

                    <td>

                        <a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_COWPAY_EDIT_COWPAY'); ?>">

                            <?php echo $row->name; ?>

                        </a>

                    </td>



                    <td align="center">

                        <?php echo $row->phone; ?>

                    </td>

                    <td align="center">

                        <?php echo $row->email; ?>

                    </td>

                    <td align="center">

                        <?php echo $row->amount; ?>

                    </td>

                    <td align="center">

                        <?php echo $row->method; ?>

                    </td>

                    <td align="center">

                        <?php echo $row->status; ?>

                    </td>


                    <td align="center">

                        <?php echo $row->reference_id; ?>

                    </td>

                    <td align="center">

                        <?php echo $row->created_at; ?>

                    </td>



                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

    <input type="hidden" name="task" value=""/>

    <input type="hidden" name="boxchecked" value="0"/>

    <?php echo JHtml::_('form.token'); ?>

</form>