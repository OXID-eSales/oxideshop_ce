<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxnewslistTest extends OxidTestCase
{

    public $aNews = array();

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        // cleaning
        $this->aNews = array();

        $this->aNews[0] = oxNew('oxnews');
        $this->aNews[0]->setId(1);
        $this->aNews[0]->oxnews__oxshortdesc = new oxField('Test 0', oxField::T_RAW);
        $this->aNews[0]->oxnews__oxactive = new oxField(1, oxField::T_RAW);
        $this->aNews[0]->oxnews__oxdate = new oxField('2007-01-01', oxField::T_RAW);
        $this->aNews[0]->save();

        $this->aNews[1] = oxNew('oxnews');
        $this->aNews[1]->setId(2);
        $this->aNews[1]->oxnews__oxshortdesc = new oxField('Test 1', oxField::T_RAW);
        $this->aNews[1]->oxnews__oxactive = new oxField(1, oxField::T_RAW);
        $this->aNews[1]->oxnews__oxdate = new oxField('2007-01-02', oxField::T_RAW);
        $this->aNews[1]->save();

        $oNewGroup = oxNew('oxbase');
        $oNewGroup->init('oxobject2group', "core");
        $oNewGroup->oxobject2group__oxobjectid = new oxField($this->aNews[1]->getId(), oxField::T_RAW);
        $oNewGroup->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oNewGroup->Save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        foreach ($this->aNews as $oNew) {
            $oNew->delete();
        }
        parent::tearDown();
    }

    /**
     * Testing news list loading
     */
    // no user
    public function testLoadNewsNoUser()
    {
        $oNewsList = new oxnewslist();
        $oNewsList->loadNews();

        $this->assertEquals(1, $oNewsList->count());
        $oItem = $oNewsList->current();
        $this->assertEquals(1, $oItem->getId());
    }

    // admin user
    public function testLoadNewsAdminUser()
    {
        $oUser = new oxuser();
        $oUser->load('oxdefaultadmin');

        $oNewsList = new oxnewslist();
        $oNewsList->setUser($oUser);
        $oNewsList->loadNews();

        $this->assertEquals(2, $oNewsList->count());

        $oNewsList->rewind();
        $oItem = $oNewsList->current();
        $this->assertEquals(2, $oItem->getId());

        $oNewsList->next();
        $oItem = $oNewsList->current();
        $this->assertEquals(1, $oItem->getId());
    }

    // admin user
    public function testLoadNewsAdminUserLimit()
    {
        $oUser = new oxuser();
        $oUser->load('oxdefaultadmin');

        $oNewsList = new oxnewslist();
        $oNewsList->setUser($oUser);
        $oNewsList->loadNews(0, 1);

        $this->assertEquals(1, $oNewsList->count());
        $oItem = $oNewsList->current();
        $this->assertEquals(2, $oItem->getId());
    }

    /**
     * Testing user setter/getter
     */
    public function testSetUserAndGetUser()
    {
        $oUser = new oxUser();
        $oUser->xxx = 'yyy';

        $oNewsList = new oxnewslist();
        $oNewsList->setUser($oUser);
        $this->assertEquals($oUser, $oNewsList->getUser());
    }
}
